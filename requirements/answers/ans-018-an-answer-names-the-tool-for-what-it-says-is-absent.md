---
id: R-ANS-018
title: 'An answer names the tool for what it says is absent'
status: held
restsOn: [D-ANS-031]
heldBy:
  - PackageSourcesTest::aMissThatOffersARequeryNamesTheCorpusToAskNext
  - PackageSourcesTest::aMissWithNoRequeryToOfferNamesBothCorporaThatAnswer
  - ProjectTest::whatACoreCheckoutDoesNotDeclareIsSaidWithTheToolThatHasIt
---

# R-ANS-018 — An answer names the tool for what it says is absent

**An answer that says something is not here names the tool that has it.**

An absence reads as a dead end, and the caller goes looking by hand — where
nothing checks what it finds against the checkout it is standing in.

## From

A session in a core checkout told by `typo3_project_describe` that "the core's
testing suites do not" exist among its declared commands, and given no tool that
has them: it reported preferring a `Build/bin/phpunit` that checkout has no
directory for (`feedback/2026-08-01-114807`, 2026-08-01). The same gap from the
other end the next day — four `gerrit:setup` hook installers answered "what can
I run here" while `Build/Scripts/runTests.sh`, called about thirty times that
session, was named nowhere (`feedback/2026-08-02-144350`).
