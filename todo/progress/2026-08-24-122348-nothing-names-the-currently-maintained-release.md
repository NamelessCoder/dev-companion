# Place the maintained release lines where a task that names a branch passes

**Serves:** feedback/2026-08-24-122348-nothing-names-the-currently-maintained-release.md
**Priority:** normal
**Branch:** todo/nothing-names-the-currently-maintained-release
**Claimed:** 2026-08-24

Choose the call that carries the lines and add the field:
`typo3_project_describe`, which every session is told to call first and which
already names the installed version, or `typo3_gerrit_lookup`, which already
returns the change's target branch. `ReleaseLines::state()`, `describe()` and
`releasable()` answer it already, so the work is the field, the output schema,
the test and the requirement that names the carrier — nothing about TYPO3 has to
be read. `D-ANS-104` is the judgement and what set this above `low`: the fact is
held and verified, and the placement is one field in an answer already being
returned. `D-ANS-073` is the boundary — state the lines and their windows, never
which of them a change belongs on.
