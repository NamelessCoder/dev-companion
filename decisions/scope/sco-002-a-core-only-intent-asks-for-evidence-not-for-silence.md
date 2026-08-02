---
id: D-SCO-002
date: 2026-07-29
status: open
---

# D-SCO-002 — A core-only intent asks for evidence, not for silence

**A core-only intent needs positive evidence of core work; where nothing says
either way it is demoted to a conditional match rather than dropped or stated.**

`outsideCore` was to be the gate for the patch submission intent. It is not
enough: the reported task — "Maintain and extend the third-party TYPO3
extension bk2k/bootstrap-package … review TCA …" — does not trip a single
outside-core marker, because "third-party TYPO3 extension" is not the phrase
the list carries. Gating on the flag alone would have left the feedback's own
case answered exactly as before.

## Decided

- A core-only intent needs positive evidence — a `typo3/sysext/` path, or
  Gerrit, Forge, "TYPO3 core" named outright. Outside the core it is dropped,
  and where nothing says either way it is demoted to the conditional match the
  catalog already models, so the answer offers it rather than states it.

## Assumed

- `coreOnly` is a property of the intent, not of the task, and patch submission
  is currently the only one. Deprecation, breaking change and changelog were
  considered and left alone: their subject is real work outside the core too,
  and it is their `checks` that are core-only, which R-SCO-002 handles.

## Wrong if

- A core contributor's task text names neither a sysext path nor Gerrit and
  they now get the submission rules as conditional rather than as fact. That is
  the cost of not guessing, and the condition line is what keeps it cheap.

## Covered by

- `ScopeTest::aCoreTaskThatNamesNeitherAPathNorGerritKeepsTheSubmissionRules`
- `ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact`
- `ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore`
- `ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission`
- `ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll`
