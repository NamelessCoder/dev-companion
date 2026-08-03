# Decide whether the `area` keeps its place in the `scopes` array

**Serves:** feedback/2026-08-03-144410-task-review-core-commit-9f6c6eb9093-110359-and.md
**Priority:** normal
**Waiting on:** deprecate `area` and take it out, or keep it and mark its entry
    as an area rather than a path? `D-SCO-009` names the first and the session
    that hit it names the second. `AGENTS.md` asks for fields to be added rather
    than renamed, which argues for the marker; the entry argues that a parameter
    whose value lands in the answer as a path nobody named is the parameter that
    has to go. Keeping it exactly as it is, and saying so in the description, is
    the third answer.

`typo3_task_guide` appends the `area` string to `paths` and places it like a
file, so a call that passes both gets its area back as an entry of `scopes` with
a scope verdict on it. A review of core commit `9f6c6eb9093` passed five
`typo3/sysext/` paths and the area
`core Resource / extbase Service / fluid ViewHelpers`, and read
`{"path": "core Resource / extbase Service / fluid ViewHelpers", "scope": "core"}`
back out of `scopes` (`feedback/2026-08-03-144410`, reproduced on 2026-08-03).
Nothing downstream can then read `scopes` as the files of the task. That is the
second **Wrong if** of
[`D-SCO-009`](../../decisions/scope/sco-009-the-brief-is-one-brief-and-names-the-paths-a-step.md),
reached at its harmless end — the area described the same subsystems the paths
were in, so the verdict it was given was the one the files got, and no step of
the brief was filtered by it. The `## Since then` section of that entry has the
reading. Both repairs hold and both change a declared schema, so the answer
decides it and the change is made in `src/Tool/TaskGuide.php`, the shared
`Result\Schema::scopes()` where the marker is the answer, and
`bin/cli tools:index` and `bin/cli tools:record` after it.
