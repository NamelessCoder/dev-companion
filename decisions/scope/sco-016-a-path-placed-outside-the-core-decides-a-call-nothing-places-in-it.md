---
id: D-SCO-016
title: A path placed outside the core decides a call nothing places in it
date: 2026-08-28
status: open
coveredBy:
  - ScopeTest::aPathNothingPlacesIsStillAnsweredFromTheCore
  - ScopeTest::aTestFileNothingPlacesIsInTheRepositoryTheCallIsIn
---

# D-SCO-016 — A path placed outside the core decides a call nothing places in it

**Where one path in a call is placed outside the core and no path is placed in
it, the call is outside the core.**

A call whose paths are one extension file and one the layout places nowhere is
answered today with the core's suites, its checks and its checklist. The
whole-call verdict falls back to the task text where the groups disagree, so the
one signal that placed anything is the one the answer drops.

## Evidence

- **Measured through `typo3_task_guide` in this checkout on 2026-08-28**, with
  the same task and `changeType: "test"` throughout:

  | paths              | call scope | checks | suites | `runTests.sh` |
  | ------------------ | ---------- | ------ | ------ | ------------- |
  | extension          | extension  | 0      | 0      | 2             |
  | unplaced           | uncertain  | 4      | 4      | 14            |
  | extension+unplaced | uncertain  | 4      | 4      | 15            |
  | core+extension     | uncertain  | 4      | 4      | 14            |

- **Only the third row is wrong.** The second is `D-SCO-008`: nothing placed the
  work, so the answer is the core's and the notice says so. The fourth is
  `D-SCO-009`: something really is in the core, and the notice names the paths
  the core's steps are not for.
- **The notice claims something untrue in that row.**
  `Scope::outsideCoreAmong()` says the checks "are steps for the paths that are
  in it and for none of the others" — and no path is in it. The caller is told
  the core suites belong to their own test file.
- **`typo3_test_run_guide` splits the same way.**
  `Classes/Parser/AbstractParser.php` alone returns no suite, which is
  `R-SCO-002` held by
  `ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`; that
  path with `Tests/Functional/Parser/ScssParserTest.php` beside it returns four
  suites and names the script 21 times.
- **This is the path set a session actually sent.**
  [`feedback/2026-08-28-001345`](../../feedback/archive/2026-08-28-001345-task-guide-routes-an-extension-test-task-at.md)
  passed exactly those two paths. It was answered `extension` for both because
  its installation placed the test file; a caller whose installation cannot be
  read gets the core's answer for the same work.
- **What leaves a path unplaced is its shape.** `Classes/` is an extension
  layout marker and `Tests/Functional/` is not, so the test file is `uncertain`
  wherever no installation knows the package.

## Decided

- **Queued rather than made here.** The gate is `src/` and every tool that
  places work reads it, which is the half `D-FBK-052` does not relax and the
  reason `D-SKL-080` queued the same class of repair.
- **Which of two shapes it takes is the card's first step**: `$outsideCore`
  widened where no group is core work, or `Scope` deciding a call's verdict from
  the groups rather than falling back to the task text. The second one settles
  `scope` as well, which is the field the answer contradicts itself with today.
- **At `high`.** It is the shape a real session sent, it spans two tools, and
  what it hands over is a command that cannot run.

## Assumed

- That the paths of one call belong to one repository, which is `D-SCO-007`'s
  reading applied to a group that places nothing.

## Wrong if

- A session working in the core passes a path the layout does not place — a
  sitepackage below `packages/` in a core monorepo — and loses the suites the
  rest of the call needs. Then one mis-placed path costs the payload where it
  used to cost a notice.
- A caller reports the uncertain notice gone from a call that still cannot say
  where the work is. The fallback exists so an unplaced call is answered rather
  than refused, and widening it must not take that.

## Since then

The second shape landed the same day: `Scope::everyPlacedPathIsOutsideTheCore()`
states the rule once and `TaskGuide` and `TestRunGuide` both read it. The third
row of the table answers `extension` now, with no check and no suite, and the
other three are unchanged. One shape moved without being reported — a core path
beside an unplaced one answers `core` where it answered `uncertain`, which is
the same rule read from the other side.
