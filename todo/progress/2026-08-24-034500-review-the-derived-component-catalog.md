# Review the derived component catalog

**Serves:** D-CAT-008
**Priority:** normal
**Branch:** catalog-derived-from-the-core
**Claimed:** 2026-08-24

Read the branch and say whether the derivation is what should be shipped. That
was the arrangement it was written under, nothing on it is blocked, and merging
is the step after the reading. It runs green — `composer ci`,
`bin/cli repository:check` and `bin/cli checkouts:verify` — and every commit on
it names what it settled.

## What is on it

- `bin/cli components:derive` reads the `backend.css` each covered branch
  commits and writes `component-classes.json`, `custom-elements.json` and
  `styleguide-listing.json`: 242 classes with 39 of them placed, 137 custom
  elements, 31 listed components, in under half a second and without an
  installation. `components:check` re-derives and fails where the committed
  files have fallen behind.
- `typo3_component_lookup` answers where a borrowed class sits, and the range is
  the class's own — so `table-fit` is answered on 12.4, where the entry's
  aggregate class range withheld it. A class the stylesheet places above its
  component comes back under `wrapping` rather than among the modifiers.
- `knowledge/documents/any/backend/using-the-styleguide.md`, and the styleguide
  listing read as the public API boundary.
- The asset build draft, with the review of 2026-08-24 worked in.

## What it does not do, and where each is written

- The mapping from a curated entry to a styleguide action, which is 16 of 26 by
  name and 10 by judgement — `todo/open/2026-08-24-014500`, which also carries
  the 15 listed components no entry answers for.
- Whether a single class is public. The templates cannot say, and that todo
  names what could.
- The frontend half of the asset build workflow, which waits on a document —
  `todo/open/2026-08-24-000156`.
- The directory `knowledge/catalog/`, which holds three subjects under one word,
  and the tool `typo3_catalog_scope`. The commands were split on 2026-08-24 and
  those two were not.
