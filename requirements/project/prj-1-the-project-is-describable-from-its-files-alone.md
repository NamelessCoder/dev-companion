---
id: R-PRJ-1
status: held
---

# R-PRJ-1 — The project is describable from its files alone

**The project around the installation is describable from its files alone: its
TYPO3 and PHP constraints, the extensions that are its own rather than TYPO3's,
the sites it configures with the sets they depend on, and the commands it
declares.**

No console, no database, so it answers on a fresh clone.

**From:** three sessions asking for a project mode, and a guide that
recommended `runTests.sh` to repositories that declare `composer t3g:cgl`
(2026-07-29).

**Held by:** `ProjectTest::theProjectIsDescribedFromItsFilesAlone`,
`ProjectTest::withoutAnInstallationThereIsNoProjectToDescribe`
