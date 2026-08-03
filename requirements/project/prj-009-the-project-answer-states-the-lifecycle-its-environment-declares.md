---
id: R-PRJ-009
status: held
restsOn: [D-ANS-044]
---

# R-PRJ-009 — The project answer states the lifecycle its environment declares

**Where the repository's environment declares commands that run without being
asked for, the project answer states them: the stage each one fires at and the
command it runs.**

Read from that environment's own files, so
[`R-PRJ-001`](prj-001-the-project-is-describable-from-its-files-alone.md) holds:
no console, no database, nothing started, and an answer on a fresh clone.

The commands list is what a caller reads for "what can I run here", and
[`R-PRJ-007`](prj-007-a-declared-command-says-whether-running-it-changes-anything.md)
holds it to what a manifest declares. An environment that installs dependencies
on start, updates the schema on import and creates a backend user after it runs
more than the manifest holds, and none of that reaches the answer. Silence there
costs more than an absence would: the four fields that do come back describe the
environment, so the answer reads as complete while the executable half of it is
missing.

## From

`feedback/2026-08-03-154501` (2026-08-03), a boot of an existing Composer project
from a fresh clone in `/home/benji/projects/site-demo-typo3-org`, whose every
step came from reading `.ddev/config.yaml` and `.ddev/providers/dump.yaml` by
hand. Re-run the same day: `environment` is still `via`, `php`, `source` and
`entered`, and `commands` still holds the one composer script.

## Held by

- `ProjectTest::theAnswerStatesWhatTheEnvironmentRunsWithoutBeingAsked`
- `ProjectTest::aHookAConfigBesideTheBaseOneTakesAwayIsNotStillReported`
- `ProjectTest::aPullRecipeDdevWroteIsNotOneThisRepositoryDecidedOn`
