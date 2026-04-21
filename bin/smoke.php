<?php

declare(strict_types=1);

/**
 * CLI smoke test that mirrors app.js classify(): paste a composer.json, print
 * which plugins land in Ready / In Progress / Not yet ready / Unknown / Other.
 * Not shipped to users; this is how we verify plugins.json against a real tree.
 *
 * Usage: php bin/smoke.php fixtures/composer-akoro.json
 */

$argv1 = $argv[1] ?? null;
if (!$argv1) {
    fwrite(STDERR, "usage: php bin/smoke.php <composer.json>\n");
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
        if (!empty($entry['notes']) && preg_match('/in\s*progress|alpha|beta|rc|v2\s*branch|work\s*in\s*progress/i', $entry['notes'])) {
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
