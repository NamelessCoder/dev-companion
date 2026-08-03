---
id: D-SCO-009
date: 2026-08-02
status: open
---

# D-SCO-009 — The brief is one brief, and names the paths a step is not for

**`typo3_task_guide` takes the paths of the work and places each of them, and
the brief it composes stays one brief.**

The hints are matched per group and named per path. The checklist, the checks
and the discovery steps are one list, and it names the paths the core's own
steps are not for.

`D-SCO-008` left this tool with one verdict because it was asked about one
`area`, and called that a missing parameter rather than a rule. The parameter is
here. What it does not do is answer twice.

## Evidence

- `META-03` is the prompt: an extension file and a core file in one session,
  because the bug may be in either. It reached this tool as one question and got
  one answer, while the two path tools already answered per path.
- The checklist, the checks and the discovery steps are already filtered entry
  by entry by `Scope::isCoreOnly()`, so what a mixed call was missing is not a
  second filtered list — it is which paths the filtered one is for.

## Decided

- The hints are matched per group and rendered per group under a `# For <path>`
  heading, which is the shape `typo3_architecture_lookup` already answers in.
  The payload merges them, as it does there.
- Everything else stays one list. Where every path is outside the core the brief
  drops the checks, the core-only checklist items and the discovery steps as
  before; where one path is in the core it keeps them and the notice names the
  paths they are not for.
- `scope` stays and means the call as a whole. It is the one group's scope where
  there is one group, and where the paths disagree it is what the task text and
  the area say on their own. `scopes` is the per-path answer, in the shape the
  two path tools already carry.
- The `area` counts as one of the paths, so a call that names only an area is
  decided exactly as it was.

## Assumed

- A caller that named two repositories wants one brief about its session, not
  two briefs to reconcile. `D-SCO-008`'s first **Wrong if** is the same worry
  from the other side, and it is why the checklist was not split.

## Wrong if

- A mixed call's checklist is followed for the extension path, changelog file
  and all, because the notice above it was read as prose and the list as the
  answer. The fix is then to filter the list to what every path shares and hand
  the core-only steps over separately, not to answer twice.
- Callers pass `paths` and `area` at once with different subjects, so the area
  arrives in `scopes` as a path nobody named. Then `area` is what has to go,
  which is a removal `AGENTS.md` does not allow of a schema — so it would be
  deprecated in its description first.

## Covered by

- `ScopeTest::aBriefForPathsOfDifferentAudienceSaysWhichStepsAreForWhich`
- `ScopeTest::aBriefForExtensionPathsAloneKeepsNoCoreStep`

## Since then

The second **Wrong if** has been reached, at the harmless end of what it
describes. A core patch review passed five `typo3/sysext/` paths and the `area`
`core Resource / extbase Service / fluid ViewHelpers` in one call, and reported
the payload it got back: the area arrived as the sixth entry of `paths` and as
`{"path": "core Resource / extbase Service / fluid ViewHelpers", "scope": "core"}`
in `scopes` (`feedback/2026-08-03-144410`, 2026-08-03). The call was made again
on this branch and reproduces both entries exactly.

The two subjects agreed, which is the half of the bullet that did not happen.
The area describes the same three subsystems the five paths are in, so it was
scoped `core` as they were, and no step of the brief was filtered by a verdict
about a string nobody named. What the reporter names is the other half: nothing
downstream can read `scopes` as the files, because one of its entries is prose.

The repair this entry names for that case — `area` deprecated in its description
and then removed — is not the only one available. The reporter's alternative
keeps `area` and marks its entry as one, which is an addition where this entry's
is a removal, and `AGENTS.md` asks for the first and not the second. Both hold,
so which is built is not a judgement this process may make on its own. The
question is with whoever maintains this repository and
`todo/waiting/2026-08-03-171601-decide-whether-the-area-keeps-its-place-in-the-scopes-array.md`
carries it in the words it was asked in.
