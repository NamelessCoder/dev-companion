---
id: R-KNW-061
title: 'A translation file is told what a missing `target-language` costs it'
status: held
restsOn: [D-KNW-050]
---

# R-KNW-061 — A translation file is told what a missing `target-language` costs it

**A label answer states that a locale-prefixed XLF with no `target-language` is
read as the default language on v14, so its `<target>` values are silently
discarded.**

The labels then render in the source wording, and nothing is raised, logged or
deprecated. The rule that governs such a file today says what a correct one
declares. That is enough to write one and not enough to recognise one that is
already wrong, which is the direction an audit reads a rule in. So the statement
is phrased as the defect and names what is observed — English in a German
backend, a maintained translation file in the package, no error and no log line
anywhere.

The version boundary is the second half, and the statement is wrong without it.
Up to 13.4 the same file was read correctly, because the parser decided on the
language that was asked for rather than on an attribute of the file. So this is
what an upgrade to v14 does to a package that was right before it, and the
answer has to reach a session asking about an upgrade as well as one auditing a
file.

## From

`feedback/2026-08-03-164659`, a conformance audit of `EXT:guidedtour` against a
TYPO3 14.3.5 installation (2026-08-03). Its highest-impact finding — 22 German
translations discarded from a fully maintained file — came from four hops into
installed source, and the hint that governs that file offered no way to see it.

## Held by

- `HintsTest::aTranslationFileIsToldWhatAMissingTargetLanguageCostsIt`
- `HintsTest::whatAMissingTargetLanguageCostsIsWithheldFromTheBranchesItCostsNothingOn`
