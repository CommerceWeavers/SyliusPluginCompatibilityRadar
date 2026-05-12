# Runbook: `bin/verify-trackers.php`

`verify-trackers.php` is the radar's safety net against false-positive editorial drift. It runs in the daily deploy workflow and locally on demand. This document covers both modes.

## What it does

Every entry in `app.js IN_PROGRESS` claims that some Sylius 2.x compatibility effort is in flight. The validator re-checks each claim against GitHub and classifies the result:

| Verdict | What it means | Action |
|---|---|---|
| `ok` | PR open + active (or branch recent) and `lastUpdate` matches reality within 7 days | Nothing |
| `refresh` | Same as `ok` but `lastUpdate` has drifted > 7 days | Sidecar refreshes `lastUpdate` automatically |
| `soft_stale` | Open / branch active but nothing has moved in 90-180 days | Sidecar sets `stale: true` |
| `hard_stale` | Untouched for 180+ days | **BLOCKS deploy** — remove from `IN_PROGRESS` |
| `merged` | PR was merged | **BLOCKS deploy** — remove from `IN_PROGRESS` |
| `closed_unmerged` | PR was closed without merge | **BLOCKS deploy** — remove from `IN_PROGRESS` |
| `branch_deleted` | Branch URL returns 404 | **BLOCKS deploy** — remove from `IN_PROGRESS` |
| `wrong_target` | Branch tracker, but `composer.json` on that branch declares `sylius/sylius` / `sylius/core` targeting `<2.0` (plugin-v2 trap) | **BLOCKS deploy** — remove from `IN_PROGRESS`, the branch is not Sylius 2.x work |
| `unreachable` | Network / parse / auth failure on a single entry | Logged, doesn't block |

Soft drift (`refresh`, `soft_stale`) is recorded in `tracker-state.json` — a sidecar the browser merges over `IN_PROGRESS` at runtime. Identity fields (URL, label, summary) always stay sourced from `app.js`.

## Daily workflow

The deployer repo (`commerceweavers.com`) defines `.github/workflows/update-radar.yml`. The validator runs between `Build plugins cache` and `Setup SSH`:

```yaml
- name: Validate in-progress trackers
  env:
    GH_TOKEN: ${{ secrets.GITHUB_TOKEN }}
  run: php radar/bin/verify-trackers.php
```

Strict mode (default) exits non-zero if any entry hits a blocking verdict. The deploy stops; Lukasz fixes the editorial dict and re-runs.

The `GH_TOKEN` provided by GitHub Actions is sufficient — all tracker URLs point to public repos.

## Local dry-run

```
php bin/verify-trackers.php --report-only
```

Prints the same report the workflow would, but always exits 0 and writes the sidecar regardless. Use this:

- Before editing `IN_PROGRESS` by hand — confirm what the workflow would flag.
- After editing `IN_PROGRESS` — re-run, confirm clean, then commit.
- When investigating a workflow failure — reproduce locally.

For machine-readable output (e.g. piping into another tool):

```
php bin/verify-trackers.php --report-only --json
```

## Handling each blocking verdict

### `merged`

The PR finished. If a stable 2.x tag shipped, the next `bin/build-cache.php` run will set `supports2x: true` and route the plugin to Ready automatically — no editorial entry needed. **Remove from `IN_PROGRESS`** and let the resolver decide the bucket.

If no stable 2.x tag shipped yet (merge into a pre-release branch), the plugin is in `prereleaseOnly` territory; the resolver routes it to In Progress with a prerelease pill on its own. Still **remove the editorial entry** — it's redundant.

### `closed_unmerged`

Work was abandoned. **Remove from `IN_PROGRESS`**. The plugin returns to its baseline classification (typically Not yet ready). If you later see the PR reopened or a new attempt start, add a fresh entry.

### `branch_deleted`

Branch is gone upstream. Either merged into a different branch, or rebased, or actually deleted. **Remove from `IN_PROGRESS`**. If new activity surfaces, add a new entry pointing at the new branch.

### `hard_stale` (180+ days quiet)

Nothing has moved in six months. Treat as abandoned for radar purposes. **Remove from `IN_PROGRESS`**. The maintainer can always reopen, and a future entry can be added.

### `wrong_target` (plugin-v2 trap)

The branch tracker points at a branch whose `composer.json` declares Sylius 1.x. This is almost always a misreading of the branch's intent — `2.x` is the plugin's own v2 release line, not Sylius 2.x work. **Remove from `IN_PROGRESS`**.

If you believe the branch really is doing Sylius 2.x work but the composer.json hasn't been updated yet, point the editorial entry at a different branch (or a PR), or hold off until the composer.json shows it.

### `unreachable`

Single transient failure (rate limit, DNS, etc.). Re-run. If it persists for one entry, check the URL is correct. If it persists for many, check `GH_TOKEN` is valid.

## Schema reference

### `tracker-state.json` (sidecar)

```json
{
  "generatedAt": "2026-05-11T21:12:07+00:00",
  "packages": {
    "vendor/plugin": {
      "lastUpdate": "2026-04-29",
      "stale": true
    }
  }
}
```

Only entries that need an update are emitted. Keys are package names matching `IN_PROGRESS`. Browser ignores keys not in `IN_PROGRESS` (graceful for future sidecars produced by the scanner plan).

### `IN_PROGRESS` entry (in `app.js`)

```js
'vendor/plugin': {
    summary: 'Open 2.x PR: "..."',
    tracker: { type: 'pr' | 'branch', url: 'https://github.com/...', label: 'PR #N' },
    lastUpdate: '2026-04-22',  // optional, auto-refreshed by validator
    stale: true,               // optional, auto-set/cleared by validator
}
```

`summary`, `tracker`, and the entry's existence are editorial decisions that stay in `app.js`. `lastUpdate` and `stale` will be overwritten by the validator's sidecar, so hand-edits to those fields are advisory.

## When this gets retired

The scanner plan (`docs/plans/2026-04-22-001-feat-daily-github-activity-scanner-plan.md`) moves all editorial data into `plugins.json` and adds automated discovery of new in-progress PRs. Once that lands, `bin/verify-trackers.php` is rewritten to validate `plugins.json` directly (no `app.js` parsing), and this runbook is updated. Until then, the validator is the standing safety net.
