<?php

declare(strict_types=1);

/**
 * CLI smoke test. Two modes:
 *
 *   php bin/smoke.php <composer.json>
 *     Classifier smoke against a real composer.json. Composer-fixture files
 *     live outside the repo (~/Sites/CommerceWeavers/YouTube/fixtures) to
 *     keep client composer.json out of git.
 *
 *   php bin/smoke.php --resolver-fixtures
 *     Run the Packagist resolver against synthetic fixtures under
 *     tests/fixtures/p2-*.json. Locks resolvePackageFromVersions() shape
 *     against regressions, especially the prerelease-only handling
 *     introduced 2026-05-11.
 */

if (in_array('--resolver-fixtures', $argv, true)) {
    require __DIR__ . '/build-cache.php';
    exit(runResolverFixtures(__DIR__ . '/../tests/fixtures'));
}

if (in_array('--core-drift', $argv, true)) {
    exit(runCoreDriftCheck(__DIR__ . '/sylius_core_packages.php', __DIR__ . '/../app.js'));
}

if (in_array('--discovery-coverage', $argv, true)) {
    require __DIR__ . '/build-cache.php';
    exit(runDiscoveryCoverageCheck());
}

$argv1 = $argv[1] ?? null;
if (!$argv1) {
    fwrite(STDERR, "usage: php bin/smoke.php <composer.json>\n");
    fwrite(STDERR, "       php bin/smoke.php --resolver-fixtures\n");
    fwrite(STDERR, "       php bin/smoke.php --core-drift\n");
    fwrite(STDERR, "       php bin/smoke.php --discovery-coverage  (live network)\n");
    exit(1);
}

$composer = json_decode(file_get_contents($argv1), true, flags: JSON_THROW_ON_ERROR);
$db = json_decode(file_get_contents(__DIR__ . '/../plugins.json'), true, flags: JSON_THROW_ON_ERROR);
$byName = [];
foreach ($db['plugins'] as $e) {
    $byName[$e['packageName']] = $e;
}

$pkgs = [];
foreach (['require', 'require-dev'] as $key) {
    foreach ($composer[$key] ?? [] as $name => $constraint) {
        if (str_contains($name, '/')) {
            $pkgs[strtolower($name)] = is_string($constraint) ? $constraint : null;
        }
    }
}

$core = require __DIR__ . '/sylius_core_packages.php';
$detected = null;
$ready = $inProgress = $notReady = $unknown = $other = $coreList = [];
foreach ($pkgs as $name => $constraint) {
    if ($name === 'sylius/sylius') {
        $detected = $constraint;
        $coreList[] = ['name' => $name, 'constraint' => $constraint];
        continue;
    }
    if (in_array($name, $core, true)) {
        $coreList[] = ['name' => $name, 'constraint' => $constraint];
        continue;
    }
    $entry = $byName[$name] ?? null;
    if ($entry) {
        if (!empty($entry['notes']) && preg_match('/\b(in\s*progress|alpha|beta|rc|v2\s*branch|work\s*in\s*progress)\b/i', $entry['notes'])) {
            $inProgress[] = ['name' => $name, 'entry' => $entry, 'constraint' => $constraint];
        } elseif ($entry['supports2x']) {
            $ready[] = ['name' => $name, 'entry' => $entry, 'constraint' => $constraint];
        } elseif ($entry['supports1x']) {
            $notReady[] = ['name' => $name, 'entry' => $entry, 'constraint' => $constraint];
        } else {
            $unknown[] = ['name' => $name, 'reason' => 'in-db but no parseable sylius constraint', 'constraint' => $constraint];
        }
    } elseif (preg_match('/sylius|bitbag|setono|monsieurbiz|webgriffe|synolia/', $name)) {
        $unknown[] = ['name' => $name, 'reason' => 'not in radar', 'constraint' => $constraint];
    } else {
        $other[] = $name;
    }
}

echo "\nDetected Sylius: " . ($detected ?? '—') . "\n";
echo "\n=== CORE / MONOREPO (" . count($coreList) . ") ===\n";
foreach ($coreList as $c) printf("  %-55s user-pin=%s\n", $c['name'], $c['constraint']);
echo "\n=== READY FOR 2.x (" . count($ready) . ") ===\n";
foreach ($ready as $r) printf("  %-60s user-pin=%-20s radar=%s\n", $r['name'], $r['constraint'], $r['entry']['syliusConstraint']);

echo "\n=== IN PROGRESS (" . count($inProgress) . ") ===\n";
foreach ($inProgress as $r) printf("  %-60s user-pin=%-20s notes=%s\n", $r['name'], $r['constraint'], $r['entry']['notes'] ?? '');

echo "\n=== NOT YET READY (" . count($notReady) . ") ===\n";
foreach ($notReady as $r) printf("  %-60s user-pin=%-20s radar=%s\n", $r['name'], $r['constraint'], $r['entry']['syliusConstraint']);

echo "\n=== NOT YET COVERED BY RADAR (" . count($unknown) . ") ===\n";
foreach ($unknown as $r) printf("  %-60s user-pin=%-20s reason=%s\n", $r['name'], $r['constraint'], $r['reason']);

echo "\n=== OTHER PHP DEPS (" . count($other) . ") ===\n";
foreach ($other as $n) echo "  $n\n";

echo "\nTotal Sylius-identified: " . (count($ready) + count($inProgress) + count($notReady) + count($unknown)) . "\n";

/**
 * Runs every tests/fixtures/p2-*.json through resolvePackageFromVersions() and
 * compares against the fixture's `expected.entry` keys. Returns process exit
 * code (0 pass, 1 any mismatch).
 */
function runResolverFixtures(string $dir): int
{
    $files = glob($dir . '/p2-*.json');
    if (!$files) {
        fwrite(STDERR, "no fixtures found under {$dir}\n");
        return 1;
    }
    sort($files);

    $pass = $fail = 0;
    $failures = [];

    foreach ($files as $file) {
        $base = basename($file);
        $raw = file_get_contents($file);
        try {
            $fix = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (Throwable $e) {
            $fail++;
            $failures[] = "{$base}: invalid JSON ({$e->getMessage()})";
            continue;
        }
        $expected = $fix['expected'] ?? null;
        $input = $fix['input'] ?? null;
        if (!is_array($expected) || !is_array($input)) {
            $fail++;
            $failures[] = "{$base}: missing input/expected blocks";
            continue;
        }

        $expectedOutcome = $expected['outcome'] ?? 'ok';

        try {
            $entry = resolvePackageFromVersions(
                $input['name'],
                $input['versions'] ?? [],
                $input['searchRow'] ?? []
            );
        } catch (RuntimeException $e) {
            if ($expectedOutcome === 'throws') {
                $msg = $expected['throwsMessage'] ?? null;
                if ($msg !== null && $e->getMessage() !== $msg) {
                    $fail++;
                    $failures[] = sprintf(
                        "%s: threw RuntimeException but message differed\n  expected: %s\n    actual: %s",
                        $base, $msg, $e->getMessage()
                    );
                    printf("  FAIL  %s\n", $base);
                    continue;
                }
                $pass++;
                printf("  PASS  %s (threw as expected)\n", $base);
            } else {
                $fail++;
                $failures[] = sprintf("%s: unexpected throw: %s", $base, $e->getMessage());
                printf("  FAIL  %s\n", $base);
            }
            continue;
        }

        if ($expectedOutcome === 'throws') {
            $fail++;
            $failures[] = sprintf("%s: expected RuntimeException but resolver returned %s",
                $base, json_encode($entry));
            printf("  FAIL  %s\n", $base);
            continue;
        }

        $expectedEntry = $expected['entry'] ?? [];
        $diffs = [];
        foreach ($expectedEntry as $key => $want) {
            $got = $entry[$key] ?? '<missing>';
            if ($got !== $want) {
                $diffs[] = sprintf("    %s: expected %s, got %s",
                    $key,
                    json_encode($want),
                    json_encode($got)
                );
            }
        }
        if ($diffs) {
            $fail++;
            $failures[] = "{$base}:\n" . implode("\n", $diffs);
            printf("  FAIL  %s\n", $base);
        } else {
            $pass++;
            printf("  PASS  %s\n", $base);
        }
    }

    echo "\n";
    echo "Resolver fixtures: {$pass} pass, {$fail} fail\n";
    if ($failures) {
        echo "\n--- failures ---\n";
        foreach ($failures as $f) {
            echo $f . "\n";
        }
    }
    return $fail === 0 ? 0 : 1;
}

/**
 * Locks the Sylius monorepo core set against two failure modes:
 *
 *   1. Drift between bin/sylius_core_packages.php (PHP side) and the
 *      SYLIUS_CORE Set in app.js (browser side). The two have to agree, or
 *      the smoke composer.json mode and the live classifier disagree about
 *      which packages count as "Core" vs "Not yet covered".
 *
 *   2. Missing 2.x-era monorepo packages. Packages introduced or surfaced
 *      in Sylius 2.x (twig-hooks, twig-extra, admin-ui, calendar, flow-bundle,
 *      money-bundle, sylius-rector, etc.) used to fall through to the
 *      "Not yet covered" bucket, breaking the classifier for any real-world
 *      2.x composer.json. This list enforces their presence.
 */
function runCoreDriftCheck(string $phpFile, string $jsFile): int
{
    $phpList = require $phpFile;
    if (!is_array($phpList)) {
        fwrite(STDERR, "core-drift: {$phpFile} did not return an array\n");
        return 1;
    }
    $phpSet = array_unique($phpList);

    $jsSource = file_get_contents($jsFile);
    if ($jsSource === false) {
        fwrite(STDERR, "core-drift: cannot read {$jsFile}\n");
        return 1;
    }
    if (!preg_match('/const SYLIUS_CORE = new Set\(\[(.+?)\]\);/s', $jsSource, $m)) {
        fwrite(STDERR, "core-drift: could not locate SYLIUS_CORE Set in {$jsFile}\n");
        return 1;
    }
    preg_match_all("/'([^']+)'/", $m[1], $jsMatches);
    $jsSet = array_unique($jsMatches[1] ?? []);

    // Packages that must be present in both sides. Each line documents the
    // signal it carries — without these, a real 2.x composer.json renders
    // half-broken in the classifier.
    $required = [
        'sylius/sylius',                  // root package
        'sylius/core', 'sylius/core-bundle',
        // 2.x-era monorepo packages (high-download, prominent in real composer.json)
        'sylius/twig-hooks',
        'sylius/twig-extra',
        'sylius/admin-ui',
        'sylius/bootstrap-admin-ui',
        'sylius/ui-translations',
        'sylius/calendar',
        'sylius/flow-bundle',
        'sylius/money-bundle',
        'sylius/sylius-rector',
        'sylius/storage',
        'sylius/translation',
        'sylius/translation-bundle',
        'sylius/pdf-generation-bundle',
        'sylius/import-export-bundle',
    ];

    $pass = $fail = 0;
    $failures = [];

    foreach ($required as $pkg) {
        if (!in_array($pkg, $phpSet, true)) {
            $fail++;
            $failures[] = "  missing from PHP (sylius_core_packages.php): {$pkg}";
        } else {
            $pass++;
        }
        if (!in_array($pkg, $jsSet, true)) {
            $fail++;
            $failures[] = "  missing from JS (app.js SYLIUS_CORE): {$pkg}";
        } else {
            $pass++;
        }
    }

    $onlyPhp = array_values(array_diff($phpSet, $jsSet));
    $onlyJs = array_values(array_diff($jsSet, $phpSet));
    foreach ($onlyPhp as $pkg) {
        $fail++;
        $failures[] = "  in PHP only (drift, add to app.js): {$pkg}";
    }
    foreach ($onlyJs as $pkg) {
        $fail++;
        $failures[] = "  in JS only (drift, add to sylius_core_packages.php): {$pkg}";
    }

    echo "\n";
    echo "Core-drift check: {$pass} required-package assertions pass, {$fail} fail\n";
    if ($failures) {
        echo "\n--- failures ---\n";
        foreach ($failures as $f) {
            echo $f . "\n";
        }
    }
    return $fail === 0 ? 0 : 1;
}

/**
 * Live-network check that the type-based discovery escapes Packagist's
 * /search.json deep-paging cap (300 results). Without combining search.json
 * with /packages/list.json the radar misses the ~400 long-tail packages
 * that share the `sylius-plugin` composer type but sit outside the search
 * ranking window. Asserts the merged result is >500 names — well above
 * the 300 cap, well below the live registry size (~688 at time of writing)
 * to allow for organic churn.
 */
function runDiscoveryCoverageCheck(): int
{
    $threshold = 500;
    $rows = fetchPackagistByType('sylius-plugin');
    $names = array_unique(array_filter(array_map(fn($r) => $r['name'] ?? null, $rows)));
    $count = count($names);
    echo "Discovery coverage (sylius-plugin): {$count} unique names (threshold {$threshold})\n";
    if ($count <= $threshold) {
        echo "FAIL: discovery appears capped — search.json + list.json merge is not working\n";
        return 1;
    }
    echo "PASS\n";
    return 0;
}
