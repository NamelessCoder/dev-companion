---
id: R-SCO-2
status: held
---

# R-SCO-2 — outsideCore changes the payload, entry by entry

**`outsideCore` changes the payload.**

Core-only commands, checklist items and checkout discovery are dropped;
conventions that transfer stay and are marked as such. The line is drawn per
entry, not per section, because a checklist mixes both.

**From:** an answer that reported `outsideCore: true` and then returned four
`runTests.sh` suites for a repository that has no `Build/Scripts/`
(2026-07-29).

**Held by:** `ScopeTest::aBriefOutsideTheCoreKeepsNothingThatOnlyTheCoreHas`,
`ScopeTest::noRunTestsCommandIsHandedToARepositoryThatHasNoRunTests`,
`ScopeTest::anArchitectureHintKeepsItsAdviceOutsideTheCoreAndLosesItsCoreChecks`
