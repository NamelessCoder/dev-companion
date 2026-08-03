---
id: R-KNW-062
status: held
restsOn: [D-KNW-048]
---

# R-KNW-062 — The import answer says what it rewrites in a site configuration

**A site configuration carried inside an export file is answered with the base
the import overwrites it with, and when that import does not run.**

A statement that only the root page id is remapped names the route it holds for,
because the two routes differ in exactly that.

## From

A session seeding a TYPO3 14.3 installation from a distribution package: the
frontend answered 404 at the project root, and the corpus carried two sentences
that read as covering the case and pointed the other way
(`feedback/2026-08-03-162836`, 2026-08-03).

## Held by

- `HintsTest::theImportAnswerSaysWhatItRewritesInASiteConfiguration`
