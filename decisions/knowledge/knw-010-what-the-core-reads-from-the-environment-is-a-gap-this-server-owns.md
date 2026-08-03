---
id: D-KNW-010
date: 2026-08-02
status: open
---

# D-KNW-010 — What the core reads from the environment is a gap this server owns

**Which environment variables TYPO3 reads on its own is inside this server's
boundary and missing from it, so the feedback is queued rather than sent to the
manual.**

The corpus already gives the project half of the advice — read deployment
secrets from the environment — and never says which variables the core consumes
before a project wires anything. Half an answer is the state that produces a
guess: the caller is told to use the environment and left to infer what is
already read from it.

## Evidence

- The miss reproduces. `typo3_architecture_lookup` with the feedback's own task
  still answers "No architecture hint matched" and falls back to the index of 39
  ids; `bin/cli hints:probe` on the same query reaches nothing.
- `configuration-reach`, the entry this subject belongs to, chooses a
  configuration owner by where a value may differ and never mentions the
  environment. What does match is `project-repository-layout` — "read deployment
  values and secrets from environment variables" — and `R-KNW-032` states the
  same half. No file below `knowledge/` or `skills/` contains `%env(`, `getenv`
  or `TYPO3_CONTEXT`.
- The TYPO3 claim holds on all four covered branches. `SystemEnvironmentBuilder`
  reads `TYPO3_CONTEXT`, `TYPO3_PATH_ROOT` and `TYPO3_PATH_APP`, each also in a
  `REDIRECT_` form and the context also as `HTTP_TYPO3_CONTEXT`. `%env()` is a
  YAML placeholder resolved by `YamlFileLoader` through `EnvVariableProcessor`
  and `EnvPlaceholderProcessor`, so it reaches site configuration and services
  rather than `config/system/`. `TYPO3_ENCRYPTION_KEY` occurs nowhere in
  `typo3/sysext/` on 12.4, 13.4 or 14.3.
- On `main` it occurs once, in
  `Changelog/15.0/Breaking-110319-EncryptionKeyCheckedBeforeLoadingExtLocalconf.rst`,
  as the project's own `getenv('TYPO3_ENCRYPTION_KEY')` in the migration example
  — which is the inferred answer confirmed from the core's own text. The same
  entry moves the empty-key check ahead of `ext_localconf.php` loading, so an
  installation that wires the key from an extension throws on 15.0. That is a
  version boundary no reading of 13.4 would have shown.
- The tool that was broken at the time is not what the gap is:
  `typo3_documentation_lookup` answers again, and
  `queries=["encryption key environment variable"]` at 13.4 returns the Site
  Handling "Environment variables" page. `typo3_changelog_lookup` reads the
  changelog out of an installation, so the 15.0 entry is reachable only where
  one is present and never from what this server ships.

## Decided

- Step 1a of the ladder, and queued. Nothing here answers the question, and the
  shape that would is the tool the session already called.
- Not step 5. `doesNotCover` excludes operating an installation — servers,
  deployment, backups — not what its configuration files contain, and this
  server already answers the project side of the same subject.
- Not closed on the spot. Every statement has to be read on four branches, and
  the judging run has read nothing but this repository plus the four greps
  above.

## Assumed

- The question arrives as project work rather than as operations. The session
  reporting it was writing `config/system/additional.php`, which is a file in
  the repository this server answers about.

## Wrong if

- What the todo establishes turns out to be the Site Handling page restated.
  Then the subject belongs in `doesNotCover` with `typo3_documentation_lookup`
  named as the route, and `project-repository-layout` needs that pointer rather
  than a statement of its own.
- The 15.0 boundary moves again before the statement is written, which is what a
  development branch is. The `since` on the statement is then read off the
  branch rather than off this entry.
