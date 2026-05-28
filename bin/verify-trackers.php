<?php

declare(strict_types=1);

/**
 * Validates the IN_PROGRESS editorial dict in radar.php against live GitHub
 * state. Runs inside the daily deploy workflow; hard-fails (exit 1) if any
 * entry is a customer-facing false positive — PR merged or closed-unmerged,
 * branch deleted, untouched for 180+ days, or branch's composer.json no
 * longer targets Sylius >=2.0 (the "plugin-v2 trap").
 *
 * Soft drift (lastUpdate is off by more than 7 days, or entry crosses the
 * 90-day soft-stale threshold) is written to tracker-state.json — a sidecar
 * the browser merges over IN_PROGRESS at runtime, so the live radar stays
 * current without committing changes to radar.php.
 *
 * Usage:
 *   php bin/verify-trackers.php                       # strict mode (CI default)
 *   php bin/verify-trackers.php --report-only         # never exit non-zero
 *   php bin/verify-trackers.php --json                # machine-readable summary
 *   php bin/verify-trackers.php --source=path/to.php  # override input
 *   php bin/verify-trackers.php --output=path.json    # override sidecar path
 *
 * Requires:
 *   - `gh` on PATH and authenticated (GH_TOKEN env or `gh auth login`).
 *   - composer/semver (already a dep) for constraint intersection.
 */

require __DIR__ . '/../vendor/autoload.php';

use Composer\Semver\Intervals;
use Composer\Semver\VersionParser;

const SOFT_STALE_DAYS = 90;
const HARD_STALE_DAYS = 180;
const LAST_UPDATE_DRIFT_DAYS = 7;

$opts = parseOpts($argv);
$sourcePath = $opts['source'] ?? __DIR__ . '/../radar.php';
$outputPath = $opts['output'] ?? __DIR__ . '/../tracker-state.json';
$reportOnly = isset($opts['report-only']);
$asJson = isset($opts['json']);

if (!file_exists($sourcePath)) {
    fwrite(STDERR, "source file not found at {$sourcePath}\n");
    exit(1);
}

if (!ghAvailable($err)) {
    fwrite(STDERR, "gh CLI unusable: {$err}\nRun `gh auth login` or set GH_TOKEN in the environment.\n");
    exit(1);
}

try {
    $entries = parseInProgress(file_get_contents($sourcePath));
} catch (Throwable $e) {
    fwrite(STDERR, "Failed to parse IN_PROGRESS from {$sourcePath}: {$e->getMessage()}\n");
    exit(1);
}

if (!$entries) {
    fwrite(STDERR, "No IN_PROGRESS entries found in {$sourcePath}\n");
    exit(1);
}

$today = new DateTimeImmutable('now', new DateTimeZone('UTC'));
$results = [];
foreach ($entries as $entry) {
    $results[] = classifyEntry($entry, $today);
}

$buckets = bucketize($results);
$hardFails = array_merge(
    $buckets['merged'] ?? [],
    $buckets['closed_unmerged'] ?? [],
    $buckets['branch_deleted'] ?? [],
    $buckets['hard_stale'] ?? [],
    $buckets['wrong_target'] ?? [],
);

$sidecar = buildSidecar($results, $today);
file_put_contents($outputPath, json_encode($sidecar, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");

if ($asJson) {
    echo json_encode([
        'generatedAt' => $today->format('c'),
        'counts' => array_map('count', $buckets),
        'results' => $results,
        'sidecarPath' => $outputPath,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n";
} else {
    printHumanReport($results, $buckets, $outputPath, $reportOnly);
}

if ($hardFails && !$reportOnly) {
    foreach ($hardFails as $r) {
        // GitHub Actions surfaces ::error:: lines prominently in the run UI.
        printf("::error::%s — %s: %s\n", $r['package'], $r['verdict'], $r['detail']);
    }
    exit(1);
}

exit(0);

// --- arg parsing ---

function parseOpts(array $argv): array
{
    $opts = [];
    foreach (array_slice($argv, 1) as $arg) {
        if (!str_starts_with($arg, '--')) {
            continue;
        }
        $pair = explode('=', substr($arg, 2), 2);
        $opts[$pair[0]] = $pair[1] ?? true;
    }
    return $opts;
}

// --- gh wrapper ---

function ghAvailable(?string &$err = null): bool
{
    $rc = runShell(['gh', 'auth', 'status'], $out, $stderr);
    if ($rc === 0) return true;
    // gh exits non-zero when GH_TOKEN is set but no `gh auth login` was run;
    // tolerate that — `gh api` will still work with the token.
    if (getenv('GH_TOKEN') || getenv('GITHUB_TOKEN')) return true;
    $err = trim($stderr) ?: 'gh auth status failed';
    return false;
}

function ghApi(string $path, array $jqFields = []): array
{
    $cmd = ['gh', 'api', $path];
    if ($jqFields) {
        // Build a jq filter that emits each requested field as JSON.
        $expr = '{' . implode(',', array_map(fn($k) => "$k: .$k", $jqFields)) . '}';
        $cmd[] = '--jq';
        $cmd[] = $expr;
    }
    $rc = runShell($cmd, $out, $stderr);
    if ($rc !== 0) {
        // Detect 404 cleanly (branch deleted, PR vanished).
        if (preg_match('/HTTP 404|Not Found|gh: Not Found/i', $stderr)) {
            return ['__error__' => '404'];
        }
        return ['__error__' => trim($stderr) ?: "exit {$rc}"];
    }
    try {
        $decoded = json_decode(trim($out), true, flags: JSON_THROW_ON_ERROR);
        return is_array($decoded) ? $decoded : ['__error__' => 'non-array response'];
    } catch (Throwable $e) {
        return ['__error__' => 'json decode: ' . $e->getMessage()];
    }
}

function runShell(array $cmd, ?string &$out = null, ?string &$err = null): int
{
    $escaped = implode(' ', array_map('escapeshellarg', $cmd));
    $proc = proc_open(
        $escaped,
        [1 => ['pipe', 'w'], 2 => ['pipe', 'w']],
        $pipes
    );
    if (!is_resource($proc)) {
        $out = '';
        $err = 'failed to spawn ' . $escaped;
        return 1;
    }
    $out = stream_get_contents($pipes[1]);
    $err = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    return proc_close($proc);
}

// --- IN_PROGRESS parser ---

/**
 * Parses the `const IN_PROGRESS = { ... };` block from the radar source (now
 * inlined in radar.php) into a list of entries. Tolerant of the JS-style
 * single quotes, trailing commas, and nested `tracker: { ... }` objects.
 * Returns array of [packageName, summary, tracker.type, tracker.url,
 * tracker.label, lastUpdate, stale].
 */
function parseInProgress(string $source): array
{
    if (!preg_match('/const\s+IN_PROGRESS\s*=\s*\{(.*?)\n\s*\};/s', $source, $m)) {
        throw new RuntimeException('IN_PROGRESS block not found');
    }
    $body = $m[1];

    // Brace-balanced entry match: 'pkg': { ... } where ... may contain
    // nested single-level objects (tracker). PCRE recursive group does the
    // balancing.
    $pattern = "/'([^']+)'\s*:\s*(\{(?:[^{}']|'(?:[^'\\\\]|\\\\.)*'|(?2))*\})/s";
    if (!preg_match_all($pattern, $body, $matches, PREG_SET_ORDER)) {
        return [];
    }

    $out = [];
    foreach ($matches as $entry) {
        $pkg = $entry[1];
        $inner = $entry[2];
        $obj = [
            'packageName' => $pkg,
            'summary' => null,
            'tracker' => null,
            'lastUpdate' => null,
            'stale' => false,
        ];
        if (preg_match("/summary\s*:\s*'((?:[^'\\\\]|\\\\.)*)'/", $inner, $mm)) {
            $obj['summary'] = stripcslashes($mm[1]);
        }
        if (preg_match("/lastUpdate\s*:\s*'([^']+)'/", $inner, $mm)) {
            $obj['lastUpdate'] = $mm[1];
        }
        if (preg_match('/stale\s*:\s*(true|false)/', $inner, $mm)) {
            $obj['stale'] = $mm[1] === 'true';
        }
        if (preg_match("/tracker\s*:\s*\{(.*?)\}/s", $inner, $mm)) {
            $tracker = ['type' => null, 'url' => null, 'label' => null];
            $tinner = $mm[1];
            if (preg_match("/type\s*:\s*'([^']+)'/", $tinner, $tt)) $tracker['type'] = $tt[1];
            if (preg_match("/url\s*:\s*'([^']+)'/", $tinner, $tt)) $tracker['url'] = $tt[1];
            if (preg_match("/label\s*:\s*'((?:[^'\\\\]|\\\\.)*)'/", $tinner, $tt)) $tracker['label'] = stripcslashes($tt[1]);
            $obj['tracker'] = $tracker;
        }
        $out[] = $obj;
    }
    return $out;
}

// --- classification ---

function classifyEntry(array $entry, DateTimeImmutable $today): array
{
    $base = [
        'package' => $entry['packageName'],
        'editorial' => [
            'summary' => $entry['summary'],
            'lastUpdate' => $entry['lastUpdate'],
            'stale' => $entry['stale'],
        ],
        'tracker' => $entry['tracker'],
        'observed' => [],
        'verdict' => 'unknown',
        'detail' => '',
        'suggested' => null,
    ];

    $tracker = $entry['tracker'];
    if (!$tracker || !$tracker['type'] || !$tracker['url']) {
        $base['verdict'] = 'unreachable';
        $base['detail'] = 'tracker missing or malformed';
        return $base;
    }

    if ($tracker['type'] === 'pr') {
        return classifyPullRequest($base, $tracker['url'], $entry['lastUpdate'], $today);
    }
    if ($tracker['type'] === 'branch') {
        return classifyBranch($base, $tracker['url'], $entry['lastUpdate'], $today);
    }

    $base['verdict'] = 'unreachable';
    $base['detail'] = "unsupported tracker type: {$tracker['type']}";
    return $base;
}

function classifyPullRequest(array $base, string $url, ?string $editorialLastUpdate, DateTimeImmutable $today): array
{
    if (!preg_match('#github\.com/([^/]+)/([^/]+)/pull/(\d+)#', $url, $m)) {
        $base['verdict'] = 'unreachable';
        $base['detail'] = 'cannot parse PR URL: ' . $url;
        return $base;
    }
    [, $owner, $repo, $num] = $m;
    $api = ghApi("repos/{$owner}/{$repo}/pulls/{$num}",
        ['state', 'merged_at', 'closed_at', 'updated_at', 'title']);
    if (isset($api['__error__'])) {
        $base['verdict'] = $api['__error__'] === '404' ? 'unreachable' : 'unreachable';
        $base['detail'] = "gh api failed: {$api['__error__']}";
        return $base;
    }

    $state = $api['state'] ?? 'unknown';
    $merged = $api['merged_at'] ?? null;
    $closed = $api['closed_at'] ?? null;
    $updated = $api['updated_at'] ?? null;
    $base['observed'] = ['state' => $state, 'merged_at' => $merged, 'closed_at' => $closed, 'updated_at' => $updated, 'title' => $api['title'] ?? null];

    if ($state === 'closed' && $merged) {
        $base['verdict'] = 'merged';
        $base['detail'] = sprintf('PR merged %s (%d days ago)', substr($merged, 0, 10), daysAgo($merged, $today));
        $base['suggested'] = 'Remove from IN_PROGRESS. If a stable 2.x release shipped, the resolver will route it to Ready automatically.';
        return $base;
    }
    if ($state === 'closed' && !$merged) {
        $base['verdict'] = 'closed_unmerged';
        $base['detail'] = sprintf('PR closed without merge %s', substr($closed ?? '', 0, 10));
        $base['suggested'] = 'Remove from IN_PROGRESS. The 2.x work was abandoned.';
        return $base;
    }

    $age = $updated ? daysAgo($updated, $today) : null;
    if ($age !== null && $age > HARD_STALE_DAYS) {
        $base['verdict'] = 'hard_stale';
        $base['detail'] = sprintf('open but no activity in %d days (last: %s)', $age, substr($updated, 0, 10));
        $base['suggested'] = 'Remove from IN_PROGRESS. The PR is effectively abandoned.';
        return $base;
    }
    if ($age !== null && $age > SOFT_STALE_DAYS) {
        $base['verdict'] = 'soft_stale';
        $base['detail'] = sprintf('open, %d days since last update', $age);
        $base['suggested'] = 'Sidecar marks this entry stale=true.';
        return $base;
    }

    // Open and active. Refresh lastUpdate if it has drifted.
    if ($editorialLastUpdate && $updated) {
        $editorialDays = daysAgo($editorialLastUpdate, $today);
        $drift = abs($editorialDays - $age);
        if ($drift > LAST_UPDATE_DRIFT_DAYS) {
            $base['verdict'] = 'refresh';
            $base['detail'] = sprintf('open, GH updated_at is %s (editorial %s; drift %dd)',
                substr($updated, 0, 10), $editorialLastUpdate, $drift);
            $base['suggested'] = sprintf("Sidecar refreshes lastUpdate to %s.", substr($updated, 0, 10));
            return $base;
        }
    }

    $base['verdict'] = 'ok';
    $base['detail'] = $age !== null ? "open, updated {$age}d ago" : 'open';
    return $base;
}

function classifyBranch(array $base, string $url, ?string $editorialLastUpdate, DateTimeImmutable $today): array
{
    if (!preg_match('#github\.com/([^/]+)/([^/]+)/tree/(.+)$#', $url, $m)) {
        $base['verdict'] = 'unreachable';
        $base['detail'] = 'cannot parse branch URL: ' . $url;
        return $base;
    }
    [, $owner, $repo, $branch] = $m;
    $branch = rtrim($branch, '/');

    $api = ghApi("repos/{$owner}/{$repo}/branches/{$branch}", ['name', 'commit']);
    if (isset($api['__error__'])) {
        if ($api['__error__'] === '404') {
            $base['verdict'] = 'branch_deleted';
            $base['detail'] = "branch {$branch} not found on {$owner}/{$repo} — deleted or renamed upstream";
            $base['suggested'] = 'Remove from IN_PROGRESS. Branch is gone.';
            return $base;
        }
        $base['verdict'] = 'unreachable';
        $base['detail'] = "gh api failed: {$api['__error__']}";
        return $base;
    }

    $commit = $api['commit'] ?? [];
    $commitDate = $commit['commit']['committer']['date'] ?? null;
    $base['observed'] = ['lastCommit' => $commitDate, 'sha' => $commit['sha'] ?? null];

    // Plugin-v2 trap: a branch named "2.x" (or similar) that actually targets
    // Sylius 1.x is the most subtle false-positive vector. Fetch composer.json
    // from the branch and assert sylius/sylius or sylius/core intersects >=2.0.
    $targetVerdict = verifyBranchTargetsSylius2($owner, $repo, $branch);
    if ($targetVerdict !== null) {
        $base['verdict'] = 'wrong_target';
        $base['detail'] = $targetVerdict;
        $base['suggested'] = "Remove from IN_PROGRESS. Branch is the plugin's own v2 line, not Sylius 2.x work.";
        return $base;
    }

    $age = $commitDate ? daysAgo($commitDate, $today) : null;
    if ($age !== null && $age > HARD_STALE_DAYS) {
        $base['verdict'] = 'hard_stale';
        $base['detail'] = sprintf('last commit %d days ago (%s)', $age, substr($commitDate, 0, 10));
        $base['suggested'] = 'Remove from IN_PROGRESS. Branch appears abandoned.';
        return $base;
    }
    if ($age !== null && $age > SOFT_STALE_DAYS) {
        $base['verdict'] = 'soft_stale';
        $base['detail'] = sprintf('last commit %d days ago', $age);
        $base['suggested'] = 'Sidecar marks this entry stale=true.';
        return $base;
    }

    if ($editorialLastUpdate && $commitDate) {
        $editorialDays = daysAgo($editorialLastUpdate, $today);
        $drift = abs($editorialDays - $age);
        if ($drift > LAST_UPDATE_DRIFT_DAYS) {
            $base['verdict'] = 'refresh';
            $base['detail'] = sprintf('last commit %s (editorial %s; drift %dd)',
                substr($commitDate, 0, 10), $editorialLastUpdate, $drift);
            $base['suggested'] = sprintf("Sidecar refreshes lastUpdate to %s.", substr($commitDate, 0, 10));
            return $base;
        }
    }

    $base['verdict'] = 'ok';
    $base['detail'] = $age !== null ? "branch active, last commit {$age}d ago" : 'branch active';
    return $base;
}

/**
 * Returns a description string when the branch's composer.json does NOT target
 * Sylius >=2.0 (plugin-v2 trap). Returns null when targeting is correct OR
 * the composer.json cannot be fetched (we don't block on unreachable).
 */
function verifyBranchTargetsSylius2(string $owner, string $repo, string $branch): ?string
{
    // Branch may contain '/' (e.g. feature/sylius-2-compatibility); gh api
    // passes it through cleanly via the ?ref= query param.
    $api = ghApi("repos/{$owner}/{$repo}/contents/composer.json?ref={$branch}", ['content', 'encoding']);
    if (isset($api['__error__'])) {
        return null; // don't block on transient fetch failure
    }
    $encoding = $api['encoding'] ?? 'base64';
    $content = $api['content'] ?? '';
    if ($encoding !== 'base64' || !$content) {
        return null;
    }
    $raw = base64_decode(str_replace(["\n", "\r"], '', $content), true);
    if ($raw === false) {
        return null;
    }
    try {
        $composer = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
    } catch (Throwable) {
        return null;
    }
    $require = $composer['require'] ?? [];
    $candidates = ['sylius/sylius', 'sylius/core', 'sylius/core-bundle'];
    $found = [];
    foreach ($candidates as $key) {
        if (!empty($require[$key])) {
            $found[$key] = $require[$key];
        }
    }
    if (!$found) {
        return "branch composer.json declares no sylius/sylius, sylius/core, or sylius/core-bundle";
    }

    foreach ($found as $key => $constraint) {
        if (constraintIntersects2x($constraint)) {
            return null; // at least one Sylius dep targets 2.x — branch is legit
        }
    }

    // None of the discovered Sylius constraints intersect 2.x.
    $describe = implode(', ', array_map(fn($k, $v) => "{$k}: {$v}", array_keys($found), array_values($found)));
    return "branch composer.json targets Sylius 1.x only ({$describe})";
}

function constraintIntersects2x(string $constraint): bool
{
    static $parser = null;
    $parser ??= new VersionParser();
    try {
        $a = $parser->parseConstraints($constraint);
        $b = $parser->parseConstraints('>=2.0.0 <3.0.0');
        return Intervals::haveIntersections($a, $b);
    } catch (Throwable) {
        return false;
    }
}

function daysAgo(string $iso, DateTimeImmutable $today): int
{
    $d = new DateTimeImmutable(substr($iso, 0, 10), new DateTimeZone('UTC'));
    return (int) $today->diff($d)->days;
}

// --- output ---

function bucketize(array $results): array
{
    $b = [];
    foreach ($results as $r) {
        $b[$r['verdict']] ??= [];
        $b[$r['verdict']][] = $r;
    }
    return $b;
}

function buildSidecar(array $results, DateTimeImmutable $today): array
{
    $packages = [];
    foreach ($results as $r) {
        $update = null;
        if ($r['verdict'] === 'refresh') {
            // Use the observed updated_at / commit date.
            $observedDate = $r['observed']['updated_at'] ?? $r['observed']['lastCommit'] ?? null;
            if ($observedDate) {
                $update['lastUpdate'] = substr($observedDate, 0, 10);
            }
        }
        if ($r['verdict'] === 'soft_stale') {
            $update['stale'] = true;
        }
        if ($update !== null) {
            $packages[$r['package']] = $update;
        }
    }
    return [
        'generatedAt' => $today->format('c'),
        'packages' => (object) $packages, // emit `{}` not `[]` when empty
    ];
}

function printHumanReport(array $results, array $buckets, string $sidecarPath, bool $reportOnly): void
{
    $order = ['merged', 'closed_unmerged', 'branch_deleted', 'hard_stale', 'wrong_target', 'soft_stale', 'refresh', 'ok', 'unreachable'];
    $counts = [];
    foreach ($order as $verdict) {
        $counts[] = sprintf('%s=%d', $verdict, count($buckets[$verdict] ?? []));
    }
    echo "\nTracker verification (" . count($results) . " entries) — " . implode(', ', $counts) . "\n";
    echo str_repeat('─', 72) . "\n";

    foreach ($order as $verdict) {
        if (empty($buckets[$verdict])) continue;
        $label = strtoupper(str_replace('_', ' ', $verdict));
        echo "\n[{$label}]\n";
        foreach ($buckets[$verdict] as $r) {
            printf("  %s\n      %s\n", $r['package'], $r['detail']);
            if ($r['suggested']) {
                printf("      → %s\n", $r['suggested']);
            }
        }
    }
    echo "\nSidecar written: {$sidecarPath}\n";
    $blockCount = count($buckets['merged'] ?? []) + count($buckets['closed_unmerged'] ?? [])
        + count($buckets['branch_deleted'] ?? []) + count($buckets['hard_stale'] ?? [])
        + count($buckets['wrong_target'] ?? []);
    if ($blockCount > 0) {
        if ($reportOnly) {
            echo "Mode: --report-only — {$blockCount} contradictions would block in strict mode.\n";
        } else {
            echo "Strict mode: {$blockCount} contradictions — exiting non-zero.\n";
        }
    } else {
        echo "All clear.\n";
    }
}
