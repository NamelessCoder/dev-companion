---
id: R-SCO-003
title: 'A core-only intent needs evidence of core work'
status: held
restsOn: [D-SCO-002]
---

# R-SCO-003 — A core-only intent needs evidence of core work

**Core-only intents such as patch submission are not selected for work that is
not core work.**

They need positive evidence of core work — a `typo3/sysext/` path or the
contribution workflow named outright — because the words that match them
("review", "push", "submit") describe maintenance anywhere. Outside the core
they are dropped; where nothing says either way they are offered under their
condition, never stated.

## From

Third-party extension maintenance recognised as a Gerrit patch submission
(2026-07-29).

## Held by

- `ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore`
- `ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission`
- `ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll`
- `ScopeTest::aCoreTaskThatNamesNeitherAPathNorGerritKeepsTheSubmissionRules`
- `ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact`
