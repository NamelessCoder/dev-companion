---
id: R-DIS-007
title: 'The installation and the console can be named outright'
status: held
heldBy:
  - InstanceTest::aNamedInstallationThatIsNotThereIsReported
  - InstanceTest::anInstallationNamedOutrightIsReadWithoutAnySearch
  - Typo3CliTest::aStatedCommandIsUsedInsteadOfWorkingOneOut
  - Typo3CliTest::aStatedCommandThatIsNoProgramIsReported
---

# R-DIS-007 — The installation and the console can be named outright

**The installation root and the console command can be set explicitly, and the
answer says which of the two was used.**

Every layout-specific discovery failure is then a one-line fix for the user
instead of five tools silently going quiet. A stated setting that cannot be used
is reported, never quietly replaced by a discovered one.

## From

A session where two links broke at once — a moved bin-dir and a host PHP below
the required one — with no lever available (2026-07-29).
