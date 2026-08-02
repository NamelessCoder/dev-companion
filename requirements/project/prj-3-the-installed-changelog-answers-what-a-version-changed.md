---
id: R-PRJ-3
status: held
---

# R-PRJ-3 — The installed changelog answers what a version changed

**What a version changed is read from the changelog the installed core ships,
never bundled.**

A snapshot would answer for the version it was taken from; the installation's
copy answers for the version the caller runs, and the answer names the versions
it covers so a gap is visible rather than silent.

## From

"what did v14 deprecate that affects my code" answered with how to author a
deprecation (2026-07-29).

## Held by

- `PackageSourcesTest::theChangelogOfTheInstalledCoreIsSearchable`
- `PackageSourcesTest::theChangelogIsNarrowedByTypeAndVersion`
- `PackageSourcesTest::anInstallationWithoutAChangelogSaysSoRatherThanAnsweringEmpty`
