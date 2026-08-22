---
id: R-ANS-003
title: 'A component answer carries its source and its version'
status: held
---

# R-ANS-003 — A component answer carries its source and its version

**What a component answer describes is qualified by its source and version
inside the entry rather than only in a trailing block.**

When the target is the active installation, its backend CSS and JavaScript
decide component presence, classes, and custom properties; a matching installed
styleguide example supplies markup. The bundled catalog remains the curated
search index and markup fallback. For another target or without usable package
evidence, every fallback entry says which majors it was verified on and one that
was not verified there is withheld.

## From

15.0 markup handed to a caller supporting 13.4 and 14.3 (2026-07-29); and an
answer for 14.3 whose loudest version number was the 15.0 snapshot (2026-07-30).

## Held by

- `CatalogTest::theCatalogSaysHowItRelatesToTheInstallationBeingRead`
- `CatalogTest::aStatedVersionSaysWhatItDidToTheAnswer`
- `CatalogTest::theCatalogScopeSeparatesEntryValidityFromItsSourceCheckout`
- `CatalogTest::theInstalledComponentContractWinsOverTheBundledSnapshot`
- `CatalogTest::anInstalledContractDoesNotAnswerForAnotherTargetMajor`, and the
- `describesVersion` field the component schema requires (`ToolContractTest`).
