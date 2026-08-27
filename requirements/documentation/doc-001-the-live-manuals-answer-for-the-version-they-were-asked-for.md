---
id: R-DOC-001
title: 'The live manuals answer for the version they were asked for'
status: held
heldBy:
  - DocumentationTest
  - PermalinkTest
  - PermalinkTest::aManualServedFromAnotherBranchSaysWhichOneAnswered
  - ScopeTest::everyToolIsReachableThroughTheScope
  - ScopeTest::everyToolNamedInTheScopeExists
  - ToolContractTest::aToolCallAnswersWithTextAndMatchingData
  - ToolContractTest::everyToolDeclaresSchemasAndAnnotations
---

# R-DOC-001 — The live manuals answer for the version they were asked for

**Broad API, reference and tutorial questions can be answered from the official
live documentation for an explicitly selected TYPO3 version.**

Every result carries its canonical URL, document identifier, document version,
section and source; a requested release never silently falls back to another
release or `main`; no match and an unreachable service are different structured
answers. Live documentation augments rather than replaces the bundled
conventions and their version, audience and binding data. The manuals searched
are the ones a question can be about, TCA among them, and the index is searched
the way the rest of this server searches: a term is worth what it separates one
page from the others, and a compound name is taken apart on both sides — a table
of contents holds page titles, so `AssetCollector` and `FunctionalTestCase`
appear in it nowhere and the pages that answer them are titled after their
subject, which is assets and functional testing. A canonical result URL can be
passed back with the same target version to read the page as text with its
headings and code examples; a caller need not reconstruct the API from installed
sources after the right manual page was already found.

## From

`EXT-07`; and two sessions answered with whatever else carried one of their
words — TCA `inline` with PSR-14 events, the testing APIs with the
content-element pages (2026-07-30).
