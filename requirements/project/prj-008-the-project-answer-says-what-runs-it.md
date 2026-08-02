---
id: R-PRJ-008
status: open
restsOn: [D-ANS-013]
---

# R-PRJ-008 — The project answer says what runs it, not only what it declares

**Where the repository configures a local environment of its own, the project
answer states the PHP that environment runs and says that the commands it lists
are run inside it.**

Read from that environment's own files — a DDEV project states `php_version` in
`.ddev/config.yaml` — so `R-PRJ-001` holds: no console, no database, nothing
started to find out, and an answer on a fresh clone.

Without it the answer offers one number where there are two. A review holds the
declared constraint against the interpreter its own shell has, and in a
containerised project those are two different machines. The command list makes
it worse rather than better: `skills/base.md` sends every task to run the checks
in it, and nothing beside them says the shell is not where they run.

## From

`feedback/2026-07-31-193611` (2026-07-31), a conformance audit in
`/home/benji/projects/site-new` whose first finding was "PHP version mismatch
blocks all tests" — the host's 8.3.23 against a declared `^8.4`, while the
container the suite runs in has 8.4 and the tests were never blocked. Re-run on
2026-08-02: `typo3_project_scope` still answers "PHP ^8.4" and lists `composer
test:unit`, and names DDEV nowhere.

## Held by

Not built yet, so nothing holds it. The assertion belongs beside the ones that
hold `R-PRJ-001`, in `ProjectTest`, against a fixture that carries a
`.ddev/config.yaml`.
