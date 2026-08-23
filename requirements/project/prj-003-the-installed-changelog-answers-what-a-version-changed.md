---
id: R-PRJ-003
title: 'The installed changelog answers what a version changed'
status: held
heldBy:
  - PackageSourcesTest::anInstallationWithoutAChangelogSaysSo
  - PackageSourcesTest::theChangelogIsNarrowedByTypeAndVersion
  - PackageSourcesTest::theChangelogOfTheInstalledCoreIsSearchable
---

# R-PRJ-003 — The installed changelog answers what a version changed

**What a version changed is read from the changelog the installed core ships,
never bundled.**

A snapshot would answer for the version it was taken from; the installation's
copy answers for the version the caller runs, and the answer names the versions
it covers so a gap is visible rather than silent.

## From

"what did v14 deprecate that affects my code" answered with how to author a
deprecation (2026-07-29).
