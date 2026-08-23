---
id: R-SCO-002
title: 'A scope outside the core changes the payload, entry by entry'
status: held
heldBy:
  - ScopeTest::aBriefInAnExtensionRepositoryHandsBackNoCoreSuite
  - ScopeTest::aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas
  - ScopeTest::aHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks
  - ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests
  - ScopeTest::twoPathsOfDifferentAudienceInOneCallStayApart
---

# R-SCO-002 — A scope outside the core changes the payload, entry by entry

**A scope of `project` or `extension` changes the payload.**

Core-only commands, checklist items and checkout discovery are dropped;
conventions that transfer stay and are marked as such. The line is drawn per
entry, not per section, because a checklist mixes both — and in a call whose
paths have different scopes it is drawn per path as well: the suites and the
checks come back for the paths that can run them, and the ones that cannot are
named beside them.

## From

An answer that reported `outsideCore: true` and then returned four `runTests.sh`
suites for a repository that has no `Build/Scripts/` (2026-07-29).
