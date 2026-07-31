---
id: R-KNW-12
status: held
---

# R-KNW-12 — Whether an extension is part of the core is answerable

**Whether an extension is part of the core, and on which versions, is
answerable rather than recalled.**

It is asked about a package that is not installed — that is exactly when it
is asked — so it is a catalog derived from one checkout per covered version,
and a miss says the name is not a system extension there rather than that it
does not exist.

**From:** a community package cited to the user as evidence of what the core
does, corrected with "content blocks die extension ist kein core code", and a
system extension nobody knew existed until the user named it (2026-07-29).

**Held by:** `CatalogTest::whetherAnExtensionIsPartOfTheCoreIsAnswerable`,
`CatalogTest::aTargetVersionDecidesWhichExtensionsAreShipped`,
`CatalogTest::everyShippedRangeNamesACoveredVersion`, and `bin/verify-catalog`,
which re-derives the list from the checkouts
