<?php

declare(strict_types=1);

/**
 * Throwaway seed script. Pulls `type: sylius-plugin` packages from Packagist,
 * resolves the `sylius/sylius` constraint from the latest stable release,
 * and writes plugins.json for the static radar page.
 *
 * Usage: php bin/build-cache.php [--limit=N] [--only=vendor/pkg,...]
 */

require __DIR__ . '/../vendor/autoload.php';

use Composer\Semver\Intervals;
use Composer\Semver\Semver;
use Composer\Semver\VersionParser;

const PACKAGIST_SEARCH = 'https://packagist.org/search.json?type=sylius-plugin&per_page=100';
const PACKAGIST_P2 = 'https://repo.packagist.org/p2/%s.json';
const USER_AGENT = 'CommerceWeavers-Radar-Seed/1.0 (+https://commerceweavers.com)';

$options = parseArgs($argv);
$limit = isset($options['limit']) ? (int) $options['limit'] : 0;
$only = isset($options['only']) ? array_map('trim', explode(',', $options['only'])) : [];

fwrite(STDERR, "Fetching Packagist list for type:sylius-plugin…\n");
$packages = fetchAllSyliusPlugins();
fwrite(STDERR, sprintf("  found %d packages\n", count($packages)));

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
    fwrite(STDERR, sprintf("[%d/%d] %s … ", $i + 1, count($packages), $name));
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

$out = [
    'generatedAt' => gmdate('c'),
    'sourceCount' => count($entries),
    'plugins' => $entries,
];

$target = __DIR__ . '/../plugins.json';
file_put_contents($target, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
fwrite(STDERR, sprintf("\nWrote %s (%d entries)\n", $target, count($entries)));

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

function fetchAllSyliusPlugins(): array
{
    $url = PACKAGIST_SEARCH;
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
