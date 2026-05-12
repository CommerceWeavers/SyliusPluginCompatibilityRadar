# In Progress Tracker Audit — 2026-05-11

One-shot audit of all 16 entries in `app.js` `IN_PROGRESS`. Each entry was checked against GitHub for PR state / branch activity, and for branch trackers, the branch's `composer.json` was inspected to confirm the work actually targets Sylius 2.x (not the plugin's own v2 line on Sylius 1.x).

**Headline: 12 of 16 entries are misleading customers right now.** 6 are mistargeted (plugin-v2-on-Sylius-1, not Sylius 2.x work); 6 are abandoned (>180 days quiet). Only 4 are legitimately tracking active Sylius 2.x work — 1 with healthy momentum, 3 soft-stale.

## How to use this document

Walk each row top to bottom. Each entry shows:

- **Editorial today** — what `app.js` currently says.
- **GitHub reality** — what the API returned today.
- **Recommended action** — KEEP, KEEP+stale, REMOVE, REPLACE.
- **Manual check** — the one click you should do to confirm before applying the recommendation.

Click each tracker URL to spot-check. When you agree with a recommendation, mark the row done (`- [x]`). The patch can wait until you've validated.

---

## Active and accurate — KEEP as-is (1)

- [ ] **`setono/sylius-feed-plugin`**
  - Editorial today: PR #109 "Active 2.x PR in review", `lastUpdate: 2026-02-25`
  - GitHub reality: PR open, updated 75 days ago
  - Recommended action: **KEEP**. The 75-day gap is the boundary of soft-stale; will tip into `stale: true` in ~15 more days of quiet.
  - Manual check: https://github.com/Setono/SyliusFeedPlugin/pull/109 — confirm still labelled as a Sylius 2.x effort.

## Still active, mark `stale: true` — KEEP+stale (3)

- [ ] **`flux-se/sylius-payum-stripe-plugin`**
  - Editorial today: PR #69 "Sylius 2.0" on the (now Sylius-maintained) fork, `lastUpdate: 2026-02-06`
  - GitHub reality: open, 94 days since last update
  - Recommended action: **KEEP + stale: true**. The repo handover happened; PR is real but slow.
  - Manual check: https://github.com/Sylius/PayumStripePlugin/pull/69

- [ ] **`monsieurbiz/sylius-advanced-option-plugin`**
  - Editorial today: PR #21 "Sylius 2.0 Available", `lastUpdate: 2026-01-21`
  - GitHub reality: open, 110 days since last update
  - Recommended action: **KEEP + stale: true**.
  - Manual check: https://github.com/monsieurbiz/SyliusAdvancedOptionPlugin/pull/21

- [ ] **`sherlockode/sylius-mondial-relay-plugin`**
  - Editorial today: `feature/sylius-2-compatibility` branch, `lastUpdate: 2025-12-11`
  - GitHub reality: branch's last commit 151 days ago; branch's `composer.json` declares `sylius/sylius: ~2.0 || ~2.1` — verified real Sylius 2.x work
  - Recommended action: **KEEP + stale: true**.
  - Manual check: https://github.com/sherlockode/SyliusMondialRelayPlugin/tree/feature/sylius-2-compatibility

---

## Mistargeted — plugin-v2 trap, REMOVE (6)

These six entries point at branches literally named `2.x` (or similar). The branches exist and have recent commits, but the `composer.json` on each declares `sylius/core: ^1.0` — i.e. the branch is the **plugin's own v2 release line, targeting Sylius 1.x**. None of this work is about Sylius 2.x. The radar tells customers "active 2.x effort in progress" where none exists.

The scanner plan (`docs/plans/2026-04-22-001-feat-daily-github-activity-scanner-plan.md`) explicitly warned about this trap: *"Explicitly not scanned: branches matching `2.x` or `v2`. User experience says these are overwhelmingly about the plugin's own v2 release, not Sylius 2.x compatibility."* The 2026-04-21 editorial seed walked into it.

- [ ] **`loevgaard/sylius-brand-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2025-12-04`
  - GitHub reality: `2.x` branch is plugin-v2-on-Sylius-1 (`sylius/core: ^1.0`). The plugin's **real Sylius 2.x work is on the `3.x` branch** (currently the default branch), shipping as `v3.0.0-alpha.3`. The `3.x` branch's composer requires `sylius/core: ^2.0`.
  - Recommended action: **REMOVE this entry, optionally REPLACE with a new one** pointing at `https://github.com/loevgaard/SyliusBrandPlugin/tree/3.x` (label: "3.x branch", summary: "Sylius 2.x work as `v3.0.0-alpha.3` series"). Once Unit 1 ships, the resolver will route this plugin to In Progress automatically via the prerelease-only path; the editorial entry then mostly carries the human-readable summary.
  - Manual check: https://github.com/loevgaard/SyliusBrandPlugin (confirm `3.x` is default), composer.json on `3.x`.

- [ ] **`setono/sylius-abandoned-cart-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2026-03-25`
  - GitHub reality: `2.x` is the default branch and the plugin's own v2 line. composer requires `sylius/core: ^1.0`. Releases on this branch are `v1.5.0` (latest stable, Sylius 1.x) and `v2.0.0-alpha`/`alpha.2` (still Sylius 1.x per `sylius/core: ^1.0`).
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/Setono/SyliusAbandonedCartPlugin/blob/2.x/composer.json — confirm `sylius/core: ^1.0`.

- [ ] **`setono/sylius-age-verification-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2026-03-10`
  - GitHub reality: same Setono pattern. `2.x` is default, composer requires `sylius/core: ^1.0`.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/Setono/sylius-age-verification-plugin/blob/2.x/composer.json

- [ ] **`setono/sylius-google-ads-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2026-02-21`
  - GitHub reality: same trap. composer requires `sylius/core: ^1.0`. Latest tag `v2.6.0` is Sylius 1.x.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/Setono/SyliusGoogleAdsPlugin/blob/2.x/composer.json

- [ ] **`setono/sylius-plausible-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2026-01-05`
  - GitHub reality: same trap. composer requires `sylius/core: ^1.0`.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/Setono/sylius-plausible-plugin/blob/2.x/composer.json

- [ ] **`setono/sylius-trustpilot-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2022-09-09`, `stale: true`
  - GitHub reality: same trap. Also genuinely abandoned (no commits since 2022). Releases are 2022-era `v2.0.0-alpha.4` etc. with `sylius/core: ^1.0`.
  - Recommended action: **REMOVE**. Already known stale, and also mistargeted.
  - Manual check: https://github.com/Setono/SyliusTrustpilotPlugin/blob/2.x/composer.json

---

## Abandoned / hard-stale — REMOVE (5)

These trackers point at real Sylius 2.x work that has stopped moving for more than 180 days. Continuing to surface them as "In Progress" tells customers a migration path is in motion when in practice it isn't.

- [ ] **`bitbag/payu-plugin`**
  - Editorial today: PR #73 "Upgrade to Sylius 2.0 - OP-561", `lastUpdate: 2025-09-16`, `stale: true`
  - GitHub reality: PR still open, but **237 days** with zero activity.
  - Recommended action: **REMOVE**. If/when it moves again, re-add.
  - Manual check: https://github.com/BitBagCommerce/SyliusPayUPlugin/pull/73

- [ ] **`bitbag/sylius-mailchimp-plugin`**
  - Editorial today: branch `OP-557-Plugin-to-Sylius-2.0` (no `lastUpdate` set)
  - GitHub reality: branch exists, composer requires `sylius/sylius: ~2.0` (real Sylius 2.x), but **last commit was 469 days ago** (2025-01-27).
  - Recommended action: **REMOVE**. Branch is legitimate but abandoned.
  - Manual check: https://github.com/BitBagCommerce/SyliusMailChimpPlugin/tree/OP-557-Plugin-to-Sylius-2.0

- [ ] **`dedi/sylius-seo-plugin`**
  - Editorial today: PR #81 "Sylius upgrade 2.1", `lastUpdate: 2025-10-13`, `stale: true`
  - GitHub reality: PR still open, **210 days** quiet.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/dediagency/sylius-seo-plugin/pull/81

- [ ] **`monsieurbiz/sylius-order-history-plugin`**
  - Editorial today: PR #11 "WIP for Sylius 2", `lastUpdate: 2025-08-28`, `stale: true`
  - GitHub reality: PR still open, **256 days** quiet.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/monsieurbiz/SyliusOrderHistoryPlugin/pull/11

- [ ] **`setono/sylius-pickup-point-plugin`**
  - Editorial today: branch `2.x`, `lastUpdate: 2023-01-09`, `stale: true`
  - GitHub reality: **double-wrong**. The branch hasn't moved in 1218 days (~3.3 years), AND its composer declares `sylius/sylius: ~1.10.14` — same plugin-v2-on-Sylius-1 trap as the rest of Setono's lineup.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/Setono/SyliusPickupPointPlugin/blob/2.x/composer.json

- [ ] **`stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin`**
  - Editorial today: PR #221 "Refactor events to dedicated services and fix Sylius 2.0", `lastUpdate: 2025-06-27`, `stale: true`
  - GitHub reality: PR still open, **318 days** quiet.
  - Recommended action: **REMOVE**.
  - Manual check: https://github.com/stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin/pull/221

---

## Summary table

| Package | Verdict | Action |
|---|---|---|
| setono/sylius-feed-plugin | ✅ OK | KEEP |
| flux-se/sylius-payum-stripe-plugin | ⚠️ Soft stale | KEEP + `stale: true` |
| monsieurbiz/sylius-advanced-option-plugin | ⚠️ Soft stale | KEEP + `stale: true` |
| sherlockode/sylius-mondial-relay-plugin | ⚠️ Soft stale | KEEP + `stale: true` |
| loevgaard/sylius-brand-plugin | ❌ Mistargeted | REMOVE (optionally REPLACE with `3.x` branch entry) |
| setono/sylius-abandoned-cart-plugin | ❌ Mistargeted | REMOVE |
| setono/sylius-age-verification-plugin | ❌ Mistargeted | REMOVE |
| setono/sylius-google-ads-plugin | ❌ Mistargeted | REMOVE |
| setono/sylius-plausible-plugin | ❌ Mistargeted | REMOVE |
| setono/sylius-trustpilot-plugin | ❌ Mistargeted + abandoned | REMOVE |
| bitbag/payu-plugin | ❌ Abandoned 237d | REMOVE |
| bitbag/sylius-mailchimp-plugin | ❌ Abandoned 469d | REMOVE |
| dedi/sylius-seo-plugin | ❌ Abandoned 210d | REMOVE |
| monsieurbiz/sylius-order-history-plugin | ❌ Abandoned 256d | REMOVE |
| setono/sylius-pickup-point-plugin | ❌ Abandoned 1218d + mistargeted | REMOVE |
| stefandoorn/sylius-google-tag-manager-enhanced-ecommerce-plugin | ❌ Abandoned 318d | REMOVE |

## Net effect

- `IN_PROGRESS` shrinks from **16 → 4** (or 5 if you re-add loevgaard pointing at `3.x`).
- The In Progress bucket on the live radar becomes **honest**: each remaining entry corresponds to a verifiable, in-flight effort targeting Sylius 2.x.
- The future validator (Unit 3 in `docs/plans/2026-05-11-001-fix-radar-prepublish-hardening-plan.md`) will keep this honest by hard-failing the daily deploy when any entry drifts into mistargeted or abandoned state.

## Companion script note

The validator being built in this branch (`bin/verify-trackers.php`) reproduces this audit on every daily workflow run, including the composer.json target check that detects the plugin-v2 trap automatically. Once Unit 3 lands, the manual walkthrough above becomes the validator's `--report-only` output.
