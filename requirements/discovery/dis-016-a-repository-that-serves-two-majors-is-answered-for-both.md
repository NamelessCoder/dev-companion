---
id: R-DIS-016
title: 'A repository that serves two majors is answered for both'
status: held
heldBy:
  - VersionsTest::aConstraintIsReadByAskingItAboutEachCoveredMajor
  - VersionsTest::aStatedMajorSaysWhichOtherOneItLeftOut
  - VersionsTest::anExtensionThatServesTwoMajorsIsAnsweredForBoth
  - VersionsTest::theAnswerSaysWhichMajorsItWasComposedFor
---

# R-DIS-016 — A repository that serves two majors is answered for both

**A repository that declares `typo3/cms-core` for more than one covered major is
answered for all of them at once.**

`typo3_task_guide` and `typo3_hint_lookup` keep a statement that holds on any of
them, name the majors and the declaration in the answer, and report them as
`targetVersions`.

A version the caller states still narrows to that one — and where that is below
what the repository declares, the answer says so: the major it was composed for,
the ones declared beside it, and that statements holding only there are missing.
The two input descriptions say the same, so a version is stated to narrow rather
than to restate what is installed. A constraint that cannot be read falls back
to the installed version. The catalogs keep withholding per single version,
because markup that does not exist fails in a browser either way.

## From

`REVIEW-02`. The extension declares `^13.4 || ^14.3`; the lookup was filtered to
the installed 14, so
`ext_emconf.php is what makes a directory an extension outside Composer` — bound
`until: 13` — never reached the session, and the file it is about was reported
as accumulated drift (2026-07-31). The run of that afternoon then never reached
the widened answer: the session read `14.3.0` out of `typo3_project_describe`
and stated it, which the input description invited by promising the installed
version as the default, and the narrowing said nothing about itself
(2026-07-31).
