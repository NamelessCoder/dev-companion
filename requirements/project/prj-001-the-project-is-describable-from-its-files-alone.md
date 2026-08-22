---
id: R-PRJ-001
title: 'The project is describable from its files alone'
status: held
---

# R-PRJ-001 — The project is describable from its files alone

**The repository the session is standing in is describable from its files
alone.**

That is its TYPO3 and PHP constraints, the extensions that are its own rather
than TYPO3's, the sites it configures with the sets they depend on, and the
commands it declares.

No console, no database, so it answers on a fresh clone — before
`composer install` as well, where the four fields that are read out of the
installed tree wait for it and the answer says which state it is in.

## From

Three sessions asking for a project mode, and a guide that recommended
`runTests.sh` to repositories that declare `composer t3g:cgl` (2026-07-29).

## Held by

- `ProjectTest::theProjectIsDescribedFromItsFilesAlone`
- `ProjectTest::theRepositoryIsDescribedBeforeAnythingIsInstalledInIt`
- `ProjectTest::withoutAnInstallationThereIsNoProjectToDescribe`
