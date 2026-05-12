# Deployer workflow step to add

The radar's daily deploy lives in the **commerceweavers.com** repo, not this one. The workflow file is `.github/workflows/update-radar.yml`. Apply this change in a separate PR over there.

## The change

Insert a new step **between** `Build plugins cache` and `Setup SSH`:

```yaml
- name: Validate in-progress trackers
  env:
    GH_TOKEN: ${{ secrets.GITHUB_TOKEN }}
  run: php radar/bin/verify-trackers.php
```

## Why between those steps

- `Build plugins cache` regenerates `plugins.json` — the validator reads `app.js`, not `plugins.json`, so order with build-cache is not strictly required, but keeping them adjacent keeps "data refresh" steps together.
- The validator writes `tracker-state.json` to the radar root. The existing `rsync -az --delete` (lines 39-48 of the supplied yaml) already syncs every non-excluded file at the radar root, so `tracker-state.json` is picked up automatically. No `rsync --include` adjustment needed.
- Strict mode (default) exits non-zero on contradiction. Subsequent steps don't run on non-zero exits, so the `Setup SSH` and `Deploy radar` steps are gated automatically.

## Permissions

The default `GITHUB_TOKEN` is sufficient — all tracker URLs point to public repos. No PAT needed. Confirmed 2026-05-11 against the 16 entries then-current.

## What to expect on the first run after this lands

The first run will fail. Today's `IN_PROGRESS` dict carries 12 contradictions (5 `hard_stale` PRs + 7 `wrong_target` branches). The validator surfaces them as `::error::` annotations in the GitHub Actions UI. Fix the editorial dict (see `docs/audits/2026-05-11-in-progress-audit.md` for the prepared cleanup list) and re-run via `workflow_dispatch`.

## Rollback

If the validator starts blocking deploys for the wrong reason (e.g. a bug in the parser), set `--report-only` temporarily:

```yaml
- name: Validate in-progress trackers
  env:
    GH_TOKEN: ${{ secrets.GITHUB_TOKEN }}
  run: php radar/bin/verify-trackers.php --report-only
```

The sidecar still writes, the deploy still proceeds, and you get the full report without a hard gate. File an issue, fix the underlying problem, remove `--report-only`.
