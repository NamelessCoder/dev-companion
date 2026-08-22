---
id: R-ANS-023
title: 'A review answer names only changes that name the issue'
status: held
restsOn: [D-ANS-055]
---

# R-ANS-023 — A review answer names only changes that name the issue

**A change handed back for a Forge issue number is one whose commit message
carries that number.**

Both core skills treat a hit here as grounds to stop working: somebody has a
patch up, so the triage is that it is under review rather than unaddressed. A
change that matched the query and not the issue is therefore not an incomplete
answer but a wrong one, and it arrives wearing a MERGED status and a plausible
core subject.

## From

A session triaging seven RTE issues got five confident false positives, every
one of them the change whose own number equalled the issue number
(`feedback/2026-08-05-033826`). It caught them only because it had asked about
seven at once and the pattern was too regular to be real. For the issue it then
patched, the false positive said a merged change already existed.

## Held by

- `GerritTest::aChangeMatchedByItsNumberAndNotItsMessageIsNotAnswered`
- `GerritTest::theNumberInAReviewUrlIsNotTheIssueBeingNamed`
- `GerritTest::anAnswerOfNothingButFalsePositivesIsEmpty`
- `GerritTest::aChangeWhoseMessageDidNotComeBackIsJudgedByItsNumberAlone`
