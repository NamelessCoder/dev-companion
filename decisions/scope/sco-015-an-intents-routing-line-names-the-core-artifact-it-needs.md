---
id: D-SCO-015
title: An intent's routing line names the core artifact it needs
date: 2026-08-28
status: open
coveredBy:
  - ScopeTest::aRuleSectionOutsideTheCoreSaysWhereAConventionIsBound
  - ScopeTest::anExtensionChangelogTaskIsRoutedAwayFromTheCoresOwnProcedure
  - ScopeTest::anExtensionDeprecationIsCommittedUnderItsOwnRepositorysConvention
  - ScopeTest::anExtensionTestBriefRoutesTheHarnessTheExtensionHas
---

# D-SCO-015 — An intent's routing line names the core artifact it needs

**A `tools` line in `knowledge/task-intents.json` is what the core-only check
reads, so a call that works in the core alone names the artifact it works on.**

`typo3_task_guide` drops the core-only entries of a brief outside the core by
what each rendered line names (`D-SCO-003`). An intent's own routing lines go
through that check like the rest, and four of them name the core nowhere while
being the core's own call.

## Evidence

- **The session.** `/home/benji/projects/bootstrap_package` on 2026-08-28,
  `claude-opus-5[1m]`,
  [`feedback/2026-08-28-001345`](../../feedback/archive/2026-08-28-001345-task-guide-routes-an-extension-test-task-at.md).
  It classified both paths as `extension` and put `typo3_test_run_guide` first
  in `nextTools`, whose own description declines for such a path.
- **Re-run in this checkout on 2026-08-28.** `typo3_task_guide` with
  `paths: ["Classes/Parser/AbstractParser.php"]` and `changeType: "test"`
  answers `scope: "extension"` and still leads with
  `typo3_test_run_guide, for the targeted invocation form`.
- **The filter fired and this line cleared it.** The same answer's
  `typo3_commit_message_guide` entry carries the outside-core wording, so
  `Scope::isCoreOnly()` ran over the list; `for the targeted invocation form`
  names no marker.
- **The marked candidate was masked by the unmarked one.** `TaskGuide` adds a
  candidate for the same tool reading `for the targeted runTests.sh invocation`,
  which the check does drop — and `nextTools()` keeps one entry per tool, the
  intent's first.
- **Three more lines of the same shape**, read over every intent on 2026-08-28
  and measured with the same extension path: `deprecation` and `breaking` route
  `typo3_commit_message_guide with workflow="core"`, and `changelog` routes
  `typo3_rule_lookup with documentId=core/contribution/changelog` beside the
  `documentation-changelog` hint, whose own declared scope is `core`.
- **`D-SCO-002` assumed the opposite for three of them**: "it is their `checks`
  that are core-only, which `R-SCO-002` handles". Their `tools` are core-only
  too.

## Decided

- **The `tests` line names `runTests.sh`.** Outside the core both candidates for
  that tool drop, and `typo3_hint_lookup id=project-extension-tests` stands
  first — the entry the reporting session used to find the invocation form.
- **Against a second line routing `typo3_project_describe`**, which the report
  also names. An intent's tools are unconditional, so it would enter every core
  test brief as well, where the suites are the guide's.
- **Against a scope flag per line.** `D-SCO-003` weighed that and the marker is
  the mechanism this repository has; what is added here is a test asserting the
  route rather than the marker, so the next session that reorders the list finds
  out from the suite.
- **The other three are queued**, because their repair is `src/`: the two
  `typo3_commit_message_guide` lines may not simply drop — the caller still
  needs that tool, in the project wording the generic candidate carries — so the
  core-only filter has to run before the deduplication rather than after it.

## Assumed

- That a session outside the core loses nothing by not being offered
  `typo3_test_run_guide`. Its own description declines for those paths, so what
  the drop costs is the decline.

## Wrong if

- A test brief in a core checkout comes back without `typo3_test_run_guide`.
  Then the marker drops the line where it applies.
- A repository outside the core turns up carrying `Build/Scripts/runTests.sh` of
  its own, and the drop takes the one call that would have answered for it.
- A fifth intent line arrives core-only and unmarked. The test added here
  asserts one route, so the population is still held by rereading.

## Since then

The queued half landed the same day. `TaskGuide::nextTools()` drops the
core-only candidates before it keeps one entry per tool, rather than `answer()`
filtering the deduplicated list afterwards, so a line the check recognises
leaves the generic candidate for that tool standing. The four lines carry their
artefact now: the two `typo3_commit_message_guide` ones name Gerrit, which is
what `workflow="core"` is the convention of, and the two `changelog` ones name
the directory below `typo3/sysext/` the procedure writes into and the core
checkout the skeleton is read in.

What that buys is the entry the drop used to take with it. An extension
deprecation brief is answered
`typo3_commit_message_guide, before committing — its default is this repository's case`,
where it was answered `with workflow="core" and isDeprecation=true` before.

The last route of this shape was not an intent's. `Result\Prose` opens every
rendered rule section with what a range is carried by elsewhere, and half of
that line asked `typo3_test_run_guide` for a `runTests.sh` command whatever the
scope — read by `typo3_rule_lookup`, `typo3_script_lookup` and
`typo3_task_guide` alike. The renderer takes the scope its caller has already
computed and says the convention half alone outside the core. An extension test
brief named the script twice before that and names it once now, in the notice
that says it does not apply.
