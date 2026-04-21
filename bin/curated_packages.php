<?php

declare(strict_types=1);

/**
 * Curated Sylius ecosystem packages that real composer.json files import but
 * that are NOT tagged as `type: sylius-plugin` on Packagist (some use
 * `sylius-bundle`, `library`, or `project`; some are commercial / private and
 * not on Packagist at all).
 *
 * Two lists:
 *   - RESOLVABLE: consumed by bin/build-cache.php, which will fetch the latest
 *     stable tag from Packagist p2 and derive constraints via the same
 *     resolver chain used for type:sylius-plugin.
 *   - MANUAL: entries that are not on public Packagist (commercial/private).
 *     These ship with explicit honest notes and supports1x/2x = false unless
 *     verified. We do NOT fabricate 2.x readiness.
 *
 * Keep the file intentionally small. Prefer adding Packagist-discoverable
 * names to RESOLVABLE and letting the script resolve them. Only use MANUAL
 * when the package cannot be looked up.
 */

return [
    // Resolvable via Packagist p2 (vendor/name only; resolver fills the rest).
    // Add only packages that are NOT already returned by:
    //   - type:sylius-plugin
    //   - type:sylius-bundle
    //   - `sylius` name-match search
    // in bin/build-cache.php. Duplicates are filtered out by packageName.
    'resolvable' => [
        // Sylius first-party helpers that some shops pin explicitly and that
        // are not always typed as sylius-plugin.
        'sylius/fixtures-bundle',
        'sylius/theme-bundle',
        'sylius/grid-bundle',
        'sylius/resource-bundle',
        'sylius/mailer-bundle',
        // Well-known ecosystem bundles historically typed as `library` or
        // `symfony-bundle` rather than sylius-plugin.
        'payum/payum-bundle',
        'jms/serializer-bundle',
        'winzou/state-machine-bundle',
    ],

    // Not on public Packagist (commercial / private / mirrored). Ship as-is.
    // We do NOT fabricate readiness; anything asserted here is based on a
    // vendor statement and must carry a note making that explicit.
    'manual' => [
        [
            // Sylius confirms SyliusPlus supports Sylius 2.0, but the radar
            // cannot verify it from public Packagist. We honour the vendor
            // claim in the flag and carry the caveat in the note.
            'packageName' => 'sylius/plus',
            'homepage' => 'https://sylius.com/plus/',
            'latestTag' => null,
            'syliusConstraint' => null,
            'constraintFrom' => null,
            'supports1x' => true,
            'supports2x' => true,
            'downloads' => 0,
            'description' => 'Sylius Plus (commercial). Distributed via private Packagist; not resolvable by this radar.',
            'notes' => 'Vendor states SyliusPlus supports Sylius 2.0. The radar cannot verify this automatically because SyliusPlus ships via a private Packagist repository. Confirm with the Sylius team before relying on this for upgrade planning.',
        ],
    ],
];
