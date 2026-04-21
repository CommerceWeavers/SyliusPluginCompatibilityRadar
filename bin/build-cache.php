<?php

declare(strict_types=1);

/**
 * Throwaway seed script. Pulls `type: sylius-plugin` (and, since the coverage
 * expansion pass, `type: sylius-bundle` plus a general `sylius` name-match
 * search and a tag-based search) packages from Packagist, resolves the
 * `sylius/sylius` constraint from the latest stable release, and writes
 * plugins.json for the static radar page.
 *
 * Sources (merged, deduped by packageName):
 *   1. Packagist search: type:sylius-plugin
 *   2. Packagist search: type:sylius-bundle   (legacy tagging)
 *   3. Packagist search: q=sylius             (catches library/project types
 *      that are still Sylius ecosystem packages; filtered by known vendors
 *      to avoid dragging in unrelated projects)
 *   4. Packagist search: tags=sylius plugin   (catches plugins only tagged,
 *      not typed; filtered by the same vendor allowlist as the name search)
 *   5. Curated list in bin/curated_packages.php (resolvable + manual)
 *
 * Usage:
 *   php bin/build-cache.php [--limit=N] [--only=vendor/pkg,...] [--no-search=bundle,name,tags] [--only-curated]
 */

require __DIR__ . '/../vendor/autoload.php';

use Composer\Semver\Intervals;
use Composer\Semver\VersionParser;

const PACKAGIST_SEARCH_TYPE = 'https://packagist.org/search.json?type=%s&per_page=100';
const PACKAGIST_SEARCH_QUERY = 'https://packagist.org/search.json?q=%s&per_page=100';
const PACKAGIST_SEARCH_TAGS = 'https://packagist.org/search.json?tags=%s&per_page=100';
const PACKAGIST_P2 = 'https://repo.packagist.org/p2/%s.json';
const USER_AGENT = 'CommerceWeavers-Radar-Seed/1.1 (+https://commerceweavers.com)';

// Vendors we trust to be Sylius-ecosystem when a name-match search returns them.
// Anything outside this allowlist from the broad `q=sylius` query is skipped
// to avoid dragging in unrelated libraries (e.g. unrelated PHP toys named
// "sylius-something-silly"). If a legitimate vendor is missing, add it here.
const TRUSTED_SYLIUS_VENDOR_ALLOWLIST = [
    'sylius',
    'bitbag',
    'setono',
    'monsieurbiz',
    'webgriffe',
    'synolia',
    'commerce-weavers',
    'flux-se',
    'odiseoteam',
    'stefandoorn',
    'loevgaard',
    'brille24',
    'mangoweb-sylius',
    'dedi',
    '3brs',
    'madcoders',
    'nextstore',
    'friendsofsylius',
    'acseo',
    'locastic',
    'sherlockode',
    'prometee',
    'spinbits',
    'tavy315',
    'asdoria',
    'agence-adeliom',
    'cleverage',
    'akki-team',
    'abderrahimghazali',
    'fifty-deg',
    'bitexpert',
    'mollie',
    'alma',
    'payplug',
];

$options = parseArgs($argv);
$limit = isset($options['limit']) ? (int) $options['limit'] : 0;
$only = isset($options['only']) ? array_map('trim', explode(',', $options['only'])) : [];
$disabledSources = isset($options['no-search'])
    ? array_map('trim', explode(',', $options['no-search']))
    : [];
$onlyCurated = isset($options['only-curated']);

// De-dup pool keyed by packageName. The first occurrence wins for `source` hints,
// but download counts from later sources are preferred if higher (they usually
// come from the broader query).
$pool = [];

if (!$onlyCurated) {
    fwrite(STDERR, "Fetching Packagist list for type:sylius-plugin…\n");
    mergePackages($pool, fetchPackagistByType('sylius-plugin'), 'type:sylius-plugin');
    fwrite(STDERR, sprintf("  pool size now %d\n", count($pool)));

    if (!in_array('bundle', $disabledSources, true)) {
        fwrite(STDERR, "Fetching Packagist list for type:sylius-bundle…\n");
        mergePackages($pool, fetchPackagistByType('sylius-bundle'), 'type:sylius-bundle');
        fwrite(STDERR, sprintf("  pool size now %d\n", count($pool)));
    }

    if (!in_array('name', $disabledSources, true)) {
        fwrite(STDERR, "Fetching Packagist name-match q=sylius (filtered by vendor allowlist)…\n");
        $raw = fetchPackagistByQuery('sylius');
        $filtered = array_values(array_filter(
            $raw,
            fn($row) => isTrustedSyliusVendor($row['name'] ?? '')
        ));
        fwrite(STDERR, sprintf("  %d raw / %d kept after vendor allowlist\n", count($raw), count($filtered)));
        mergePackages($pool, $filtered, 'name-match');
        fwrite(STDERR, sprintf("  pool size now %d\n", count($pool)));
    }

    if (!in_array('tags', $disabledSources, true)) {
        // Packagist's web UI search at /search/?tags=sylius%20plugin sends
        // the tags param with a literal space. The JSON endpoint accepts the
        // same space (URL-encoded) and returns the same superset that the web
        // UI shows; pulling the single-tag form `sylius-plugin` misses packages
        // that only tag themselves with the two separate words.
        fwrite(STDERR, "Fetching Packagist tag-match tags=sylius plugin (filtered by vendor allowlist)…\n");
        $raw = fetchPackagistByTags('sylius plugin');
        $filtered = array_values(array_filter(
            $raw,
            fn($row) => isTrustedSyliusVendor($row['name'] ?? '')
        ));
        fwrite(STDERR, sprintf("  %d raw / %d kept after vendor allowlist\n", count($raw), count($filtered)));
        mergePackages($pool, $filtered, 'tags:sylius-plugin');
        fwrite(STDERR, sprintf("  pool size now %d\n", count($pool)));
    }
}

// Curated resolvable names get folded in too (resolved via p2 below).
$curated = require __DIR__ . '/curated_packages.php';
foreach ($curated['resolvable'] ?? [] as $name) {
    $key = strtolower($name);
    if (isset($pool[$key])) {
        continue;
    }
    $pool[$key] = [
        'searchRow' => ['name' => $name, 'downloads' => 0, 'description' => null, 'repository' => null],
        'source' => 'curated',
    ];
}

$packages = array_values(array_map(fn($p) => $p['searchRow'] + ['_source' => $p['source']], $pool));
fwrite(STDERR, sprintf("Merged pool: %d unique packages\n", count($packages)));

if ($only) {
    $packages = array_values(array_filter($packages, fn($p) => in_array($p['name'], $only, true)));
    fwrite(STDERR, sprintf("  filtered to %d via --only\n", count($packages)));
}

// Sort by downloads desc, then trim.
usort($packages, fn($a, $b) => ($b['downloads'] ?? 0) <=> ($a['downloads'] ?? 0));
if ($limit > 0) {
    $packages = array_slice($packages, 0, $limit);
    fwrite(STDERR, sprintf("  trimmed to top %d by downloads\n", $limit));
}

$entries = [];
foreach ($packages as $i => $pkg) {
    $name = $pkg['name'];
    fwrite(STDERR, sprintf("[%d/%d] %s (%s) … ", $i + 1, count($packages), $name, $pkg['_source'] ?? '?'));
    try {
        $entry = resolvePackage($name, $pkg);
        $entries[] = $entry;
        fwrite(STDERR, sprintf("tag=%s sylius=%s ready=%s\n",
            $entry['latestTag'] ?? '?',
            $entry['syliusConstraint'] ?? '?',
            $entry['supports2x'] ? '2.x' : ($entry['supports1x'] ? '1.x' : 'neither')
        ));
    } catch (Throwable $e) {
        fwrite(STDERR, sprintf("SKIP (%s)\n", $e->getMessage()));
    }
    // Tiny delay to be polite to the mirror.
    usleep(50_000);
}

// Merge curated manual entries last, but only if they're not already present
// from a resolved Packagist lookup.
$byName = [];
foreach ($entries as $e) {
    $byName[strtolower($e['packageName'])] = true;
}
foreach ($curated['manual'] ?? [] as $manual) {
    if (empty($manual['packageName'])) {
        continue;
    }
    $key = strtolower($manual['packageName']);
    if (isset($byName[$key])) {
        fwrite(STDERR, sprintf("curated/manual %s already resolved from Packagist; skipping manual override\n", $manual['packageName']));
        continue;
    }
    $entries[] = $manual + [
        'homepage' => 'https://packagist.org/packages/' . $manual['packageName'],
        'latestTag' => null,
        'syliusConstraint' => null,
        'constraintFrom' => null,
        'supports1x' => false,
        'supports2x' => false,
        'downloads' => 0,
        'description' => null,
    ];
    fwrite(STDERR, sprintf("curated/manual %s added\n", $manual['packageName']));
}

// Final safety: dedupe by packageName (lowercased).
$final = [];
foreach ($entries as $e) {
    $key = strtolower($e['packageName']);
    if (isset($final[$key])) {
        continue;
    }
    $final[$key] = $e;
}
$final = array_values($final);

// Sort final list by downloads desc so the UI and smoke test stay consistent.
usort($final, fn($a, $b) => ($b['downloads'] ?? 0) <=> ($a['downloads'] ?? 0));

$out = [
    'generatedAt' => gmdate('c'),
    'sourceCount' => count($final),
    'plugins' => $final,
];

$target = __DIR__ . '/../plugins.json';
file_put_contents($target, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
fwrite(STDERR, sprintf("\nWrote %s (%d entries)\n", $target, count($final)));

// --- helpers ---

function parseArgs(array $argv): array
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

function fetchPackagistByType(string $type): array
{
    return fetchPackagistPaginated(sprintf(PACKAGIST_SEARCH_TYPE, urlencode($type)));
}

function fetchPackagistByQuery(string $query): array
{
    return fetchPackagistPaginated(sprintf(PACKAGIST_SEARCH_QUERY, urlencode($query)));
}

function fetchPackagistByTags(string $tags): array
{
    return fetchPackagistPaginated(sprintf(PACKAGIST_SEARCH_TAGS, urlencode($tags)));
}

function fetchPackagistPaginated(string $url): array
{
    $all = [];
    while ($url) {
        $data = httpGetJson($url);
        foreach ($data['results'] ?? [] as $row) {
            $all[] = $row;
        }
        $url = $data['next'] ?? null;
    }
    return $all;
}

function mergePackages(array &$pool, array $rows, string $source): void
{
    foreach ($rows as $row) {
        $name = $row['name'] ?? null;
        if (!$name) {
            continue;
        }
        $key = strtolower($name);
        if (!isset($pool[$key])) {
            $pool[$key] = ['searchRow' => $row, 'source' => $source];
            continue;
        }
        // Prefer the higher download count if the later source has better metadata.
        $existing = $pool[$key]['searchRow'];
        if (($row['downloads'] ?? 0) > ($existing['downloads'] ?? 0)) {
            $pool[$key]['searchRow'] = $row + $existing;
        }
    }
}

function isTrustedSyliusVendor(string $packageName): bool
{
    if (!str_contains($packageName, '/')) {
        return false;
    }
    [$vendor] = explode('/', $packageName, 2);
    return in_array(strtolower($vendor), TRUSTED_SYLIUS_VENDOR_ALLOWLIST, true);
}

function resolvePackage(string $name, array $searchRow): array
{
    $url = sprintf(PACKAGIST_P2, $name);
    $data = httpGetJson($url);
    $versions = $data['packages'][$name] ?? [];
    if (!$versions) {
        throw new RuntimeException('no versions in p2');
    }

    $latestStable = null;
    foreach ($versions as $v) {
        $ver = $v['version'] ?? '';
        if (!$ver || str_contains($ver, 'dev-') || str_starts_with($ver, 'dev-')) {
            continue;
        }
        if (preg_match('/-(alpha|beta|rc|pre)/i', $ver)) {
            continue;
        }
        $latestStable = $v;
        break; // p2 is ordered newest first
    }

    $source = $latestStable ?? $versions[0];
    $tag = $source['version'] ?? null;

    // Prefer sylius/sylius; fall back to monorepo components that mirror its version.
    // (Many plugins — e.g. Setono — do not declare sylius/sylius directly.)
    $constraintSources = ['sylius/sylius', 'sylius/core', 'sylius/core-bundle'];
    $syliusConstraint = null;
    $constraintFrom = null;
    foreach ($constraintSources as $key) {
        if (!empty($source['require'][$key])) {
            $syliusConstraint = $source['require'][$key];
            $constraintFrom = $key;
            break;
        }
    }

    $supports2x = $syliusConstraint ? constraintIntersects($syliusConstraint, '>=2.0.0 <3.0.0') : false;
    $supports1x = $syliusConstraint ? constraintIntersects($syliusConstraint, '>=1.0.0 <2.0.0') : false;

    // Prefer a homepage that points somewhere useful. Packagist search row has repository.
    $homepage = $searchRow['repository']
        ?? $searchRow['url']
        ?? $source['homepage']
        ?? ('https://packagist.org/packages/' . $name);

    return [
        'packageName' => $name,
        'homepage' => $homepage,
        'latestTag' => $tag,
        'syliusConstraint' => $syliusConstraint,
        'constraintFrom' => $constraintFrom,
        'supports1x' => $supports1x,
        'supports2x' => $supports2x,
        'downloads' => (int) ($searchRow['downloads'] ?? 0),
        'description' => $searchRow['description'] ?? null,
    ];
}

function constraintIntersects(string $pluginConstraint, string $targetRange): bool
{
    static $parser = null;
    $parser ??= new VersionParser();
    try {
        $a = $parser->parseConstraints($pluginConstraint);
        $b = $parser->parseConstraints($targetRange);
        return Intervals::haveIntersections($a, $b);
    } catch (Throwable) {
        return false;
    }
}

function httpGetJson(string $url): array
{
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'header' => "User-Agent: " . USER_AGENT . "\r\nAccept: application/json\r\n",
            'timeout' => 20,
        ],
    ]);
    $raw = @file_get_contents($url, false, $ctx);
    if ($raw === false) {
        throw new RuntimeException('HTTP GET failed: ' . $url);
    }
    $data = json_decode($raw, true);
    if (!is_array($data)) {
        throw new RuntimeException('non-JSON response from: ' . $url);
    }
    return $data;
}
