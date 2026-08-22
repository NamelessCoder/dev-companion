---
id: R-KNW-071
title: 'A clone is told when DDEV writes additional.php'
status: held
restsOn: [D-KNW-085]
---

# R-KNW-071 — A clone is told when DDEV writes additional.php

**The project configuration answer says that DDEV writes
`config/system/additional.php` only where it detects an installed TYPO3, and the
operations checklist says the same.**

A clone is booted in the only order a clone allows, the environment before the
dependencies, and DDEV's detection reads the installed core. So the first start
writes no file, the site answers exception 1396795884 for the trusted hosts
pattern that file supplies, and the console reports success throughout. An
answer that says the file is rewritten on every start promises that session a
file it will not get, and points its debugging at TYPO3's exception rather than
at the write order.

## From

`feedback/2026-08-17-205850` (2026-08-17), a session building a TYPO3 14.3.6
demo site under DDEV v1.25.1 and asking that the sequence run unattended from
the state a colleague's clone is in. It met the missing file twice — on the
initial build and again on the clone-state rebuild — and diagnosed both against
TYPO3's exception, because nothing in the corpus pointed at the environment's
write timing.

## Held by

- `HintsTest::theDdevSettingsAnswerSaysWhenThatFileIsWritten`
