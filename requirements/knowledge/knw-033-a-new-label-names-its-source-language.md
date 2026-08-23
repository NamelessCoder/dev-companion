---
id: R-KNW-033
title: 'A new label names its source language'
status: held
restsOn: [D-KNW-011]
heldBy:
  - HintsTest::aNewLabelNamesTheSourceLanguageAndWhereItsTranslationGoes
---

# R-KNW-033 — A new label names its source language

**A label-authoring answer says that the source XLF is English, where a locale's
translation goes, and how a source file that is not English is corrected.**

The translation goes into the locale-prefixed file beside the source file, under
the same unit id. A non-English source file already present in a package is a
defect to report rather than a local convention to continue, and the report says
what to do about it: the source file keeps its path and its unit ids and its
wording becomes English, the wording it replaced moves into the locale-prefixed
file beside it as `<target>` under
`source-language="en" target-language="<locale>"`, and adding an `en.`-prefixed
file is not the correction — the unprefixed file is read as the `default` locale
that every other locale falls back to.

## From

A sitepackage whose German source XLF led a forward run to add every new
backend-module label in German too (2026-07-30), and an audit of a package
already in that state that offered "add en.xlf" and "switch the source to en and
add de.xlf" as equal remedies (2026-07-31).
