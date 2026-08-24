---
id: R-KNW-076
title: 'The two routes out of a generated additional.php are given in order'
status: held
restsOn: [D-KNW-085]
heldBy:
  - HintsTest::theRoutesOutOfAGeneratedAdditionalPhpAreOrdered
---

# R-KNW-076 — The two routes out of a generated additional.php are given in order

**The second start is the route out of a missing `config/system/additional.php`;
the committed project-owned file is the second choice, named with its cost and
which repository may pay it.**

The two are not symmetrical. A second start ends the ordering for good and
leaves the file where DDEV writes it; a committed file puts installation state
under version control, which a repository that deploys from the checkout already
carries and an extension repository does not. Offered as a coordinate pair, the
second reads as the route to a single-command start, and a session that takes it
in an extension repository commits `config/` and has the commit rejected.

## From

`feedback/2026-08-24-140222` (2026-08-24), a session setting a DDEV development
installation up for a TYPO3 extension on TYPO3 14.3.6. The checklist item that
answered its case told it to leave the file to DDEV, and the item beside it
offered the committed file with no condition on it; it took the second, wrote a
project-owned `additional.php`, set `disable_settings_management: true`,
un-ignored the path, and was corrected by the user against a reference
repository that does what the first item says.
