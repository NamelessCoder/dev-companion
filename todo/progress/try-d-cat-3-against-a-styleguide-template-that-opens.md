# Try `D-CAT-3` against a styleguide template that opens with scaffolding

**Serves:** decisions/
**Branch:** todo/try-d-cat-3-against-a-styleguide-template-that-opens
**Claimed:** 2026-08-01

Two failures are named: component state that exists only at runtime and appears
in neither compiled CSS nor installed JavaScript, and a styleguide template
whose first matching example is page scaffolding rather than component markup.
The second is readable from an installation below and is the one with a fix the
entry already names — an explicit selector in the curated index. What would hold
it is a `CatalogTest` fixture whose first match is scaffolding, asserting the
extractor takes the selected example rather than the first one.
