---
id: R-KNW-013
title: 'A statement lives in the category it is asked from'
status: held
heldBy:
  - HintsTest::aMenuQuestionThatReadsAsFrontendWorkStillReachesTheMenuTrap
  - HintsTest::aNavigationIsAnsweredWhereMenusAreActuallyConfigured
---

# R-KNW-013 — A statement lives in the category it is asked from

**A statement lives in the category its question is asked from, not in the one
the mechanism happens to be implemented in.**

Domains withhold whole categories, so a trap about configuring a site that sits
among the PHP hints is invisible to every query that reads as frontend work —
and re-reported as missing by a caller who was right that they could not find
it.

## From

`excludeDoktypes` reported a second time, while the sentence about it was in
`frontend-dataprocessors` — a hint about writing a processor, which a
sitepackage question never sees (2026-07-29).
