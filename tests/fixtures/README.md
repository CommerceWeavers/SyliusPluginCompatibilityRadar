# Resolver fixtures

Each `p2-*.json` file pairs a synthetic Packagist `p2/<pkg>.json` response with
the radar entry shape it should produce. Used by `php bin/smoke.php
--resolver-fixtures` to lock the resolver's classification behavior against
regressions — particularly the prerelease-only handling that landed
2026-05-11 (`docs/plans/2026-05-11-001-fix-radar-prepublish-hardening-plan.md`,
Unit 1).

## Fixture shape

```json
{
  "_doc": "human-readable description",
  "input": {
    "name": "vendor/plugin",
    "searchRow": { "name": "vendor/plugin", "downloads": 1234, "description": "...", "repository": "https://..." },
    "versions": [ /* Packagist p2 versions array, newest-first */ ]
  },
  "expected": {
    "outcome": "ok" | "throws",
    "throwsMessage": "no versions in p2"        /* when outcome is "throws" */,
    "entry": { /* expected resolver fields — only the keys listed are compared */ }
  }
}
```

When `outcome: "ok"`, the runner asserts each key in `expected.entry` matches
the resolver output exactly. Keys not listed are ignored (so we don't have to
keep `homepage` / `downloads` in lockstep with the search row).

When `outcome: "throws"`, the runner asserts a `RuntimeException` was thrown
with the expected message.

## Adding a new fixture

1. Name it `p2-<scenario>.json` so it auto-discovers.
2. Populate `input.versions` with at least the fields the resolver reads:
   `version`, `version_normalized` (optional), `require`.
3. Keep `expected.entry` minimal — list only the fields you want to lock down.
