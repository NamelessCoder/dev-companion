---
id: R-DIS-11
status: held
---

# R-DIS-11 — The entrypoint installs its own client configuration

**The entrypoint can install its own stdio configuration into the caller's
`.mcp.json` on an explicit `install` command.**

It preserves every unrelated entry, is idempotent for its own command, and
refuses to replace a `typo3-cms-mcp` entry that points somewhere else. Serving
requests remains read-only; no ordinary lookup writes client configuration.

## From

The two manual absolute-path JSON snippets between discovering the package and
being able to call it (2026-07-30).

## Held by

- `InstallerTest`
