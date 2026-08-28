---
id: D-CAT-003
title: The component index is curated; its contract comes from the installation
date: 2026-07-30
status: open
---

# D-CAT-003 — The component index is curated; its contract comes from the installation

**Where the caller targets the active installation and its backend CSS is
present, the installed files decide the component contract.**

The curated index stays the searchable subset and the complete fallback.

The component catalog was deliberately pinned to one core revision and guarded
by `bin/cli catalog:check`. In the first `EXT-04` run that safeguard was not
enough: the installation was TYPO3 14.3.5, the snapshot described main, and the
session made about twenty-five shell reads into the installed core before it
trusted the markup and classes it had already been given.

## Evidence

- Composer installations ship `EXT:backend/Resources/Public/Css/backend.css`;
  they do not ship the repository-level `Build/Sources/Sass/` tree. The compiled
  artifact is also the contract the browser actually receives and includes
  classes generated from Sass maps that a source-text search misses.

## Decided

- When `targetVersion` is the active installation and its backend CSS is
  present, installed package files win. The compiled CSS decides component
  presence, classes, and custom properties; installed JavaScript decides
  custom-element presence; and the first matching installed styleguide example
  replaces bundled markup. Every answer names the files and exact TYPO3 version
  it read.
- Derivation does not replace curation. Names, summaries, keywords, the
  component boundary, and the association with a styleguide page are judgments
  rather than an inventory a stylesheet contains. The bundled catalog remains
  that searchable subset and remains the complete fallback when no installation
  is readable, its package evidence is incomplete, or the caller targets another
  major.

## Assumed

- An `sg:example` containing the root class or custom element is a copyable
  example of that component. On a dedicated component page whose examples render
  through a ViewHelper instead of spelling the class, its first example is the
  installed usage contract. Where the installed template has no example at all,
  the answer keeps the bundled markup and labels it as fallback instead of
  pretending it was derived.

## Wrong if

- Component state is generated only at runtime and appears in neither the
  compiled CSS nor installed JavaScript, or a styleguide template's first
  matching example is page scaffolding rather than component markup. The former
  needs another installed source; the latter needs an explicit selector in the
  curated index rather than a more permissive extractor.

## Since then

The second half happened and the extractor was the wrong place to look: run over
all entries against two checkouts, five demos hand back scaffolding as the
installed markup and every one of them carries the root class correctly, so
there is nothing to be stricter about. `demoSelector` is the entry's say
instead, checked with the same `carries()` as the root class. It narrows and
never widens — a selector no example carries derives nothing, and the answer
keeps the bundled markup as a fallback rather than reverting to the scaffolding.

## Since then

Four of the five cannot be fixed by a selector: they name the component nowhere
outside the demo layout, so selecting an example would only move which
scaffolding is handed over. `demoDerives` is what says the demo shows the
component nowhere copyable, and it is a second field rather than a
`demoSelector` of `false`, which would be two rules read off one place. An entry
that derives nothing is not read at all, so its demo is not among its
`sourceFiles`. The permissive extractor and a scaffolding blocklist are both
dismissed: they would decide by a pattern what the index decides by reading. The
first half of the **Wrong if** is untried.

## Since then

Re-read on 2026-08-22. `bin/cli catalog:check` reports every demo as its entry
recorded it, the four `demoDerives` entries included, so the second half of
**Wrong if** stays where the two sections above left it. The first half is
untried for the reason it always was: state that exists only at runtime needs an
installation to look for, and a checkout cannot show it.
