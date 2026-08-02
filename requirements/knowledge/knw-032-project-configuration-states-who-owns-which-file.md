---
id: R-KNW-032
status: held
---

# R-KNW-032 — Project configuration states who owns which file

**Project configuration answers distinguish TYPO3-owned `settings.php` from
project-owned, subsequently loaded `additional.php`, and state how DDEV's
generated marker changes that ownership.**

They warn that a regenerated ignore file can hide an as-yet-untracked
deployment configuration and require checking that the project file remains
tracked. DDEV is identified as local-only: a shared project file guards its
local overrides and reads deployment secrets from the environment rather than
committing them.

## From

DDEV replacing deployment overrides and re-ignoring the replaced file in the
same project-configuration change (2026-07-30).

## Held by

- `HintsTest::projectSystemConfigurationStatesItsOwnershipBoundary`
