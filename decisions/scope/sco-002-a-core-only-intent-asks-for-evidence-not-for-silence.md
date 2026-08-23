---
id: D-SCO-002
title: A core-only intent asks for evidence, not for silence
date: 2026-07-29
status: confirmed
coveredBy:
  - ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission
  - ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules
  - ScopeTest::inASitePackageThePatchSubmissionIntentIsNotOfferedAtAll
  - ScopeTest::maintainingAnExtensionIsNotSubmittingAPatchToTheCore
  - ScopeTest::theBriefNamesWhatWouldTurnTheConditionIntoFact
---

# D-SCO-002 — A core-only intent asks for evidence, not for silence

**A core-only intent needs positive evidence of core work; where nothing says
either way it is demoted to a conditional match rather than dropped or stated.**

`outsideCore` was to be the gate for the patch submission intent. It is not
enough: the reported task — "Maintain and extend the third-party TYPO3 extension
bk2k/bootstrap-package … review TCA …" — does not trip a single outside-core
marker, because "third-party TYPO3 extension" is not the phrase the list
carries. Gating on the flag alone would have left the feedback's own case
answered exactly as before.

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

- A core contributor's task text names neither a sysext path nor Gerrit and they
  now get the submission rules as conditional rather than as fact. That is the
  cost of not guessing, and the condition line is what keeps it cheap.

## Confirmed on 2026-08-02

The **Wrong if** happened as written and costs what the entry said it would.
`CORE-07` is the case: «The page tree loses drag and drop as soon as a mount
point is set. Fix that, then take me through pushing it for review.» names
neither a `typo3/sysext/` path nor Gerrit, and the brief answers "Possibly also:
Patch submission, if the patch goes to the TYPO3 core itself" — one line, one
condition, and the submission steps under it rather than dropped. That is the
demotion this decided, met in the one text it was predicted to be met in. What
keeps it cheap is that the condition is a sentence the contributor settles from
their own intent, and naming a sysext path in the same session turns it into a
stated match; `ScopeTest::aCoreTaskNamingNoPathKeepsTheSubmissionRules` and
`ScopeTest::aCorePathStillMakesTheSameWordAPatchSubmission` hold both halves.
