---
id: D-CAT-003
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

The second half happened, and the extractor was the wrong place to look for it.
Run over all 25 entries against `.checkouts/14.3` and `.checkouts/main` on
2026-08-02, five demos hand back scaffolding as the component's installed
markup, and none of them does so by being sloppy about the root class:
`Cards.fluid.html` opens with a card wrapped in a `<form>` of switches,
`Input.fluid.html` and `Buttons.fluid.html` with the styleguide's own
`example-container` grid, `StatusIndicators.fluid.html` with an
`indicators-grid` looping `<f:for each="{states}">` over a variable only its
controller sets, `Dropdown.fluid.html` with an inline-styled flex row. Every one
of them carries the root class, correctly — the page is built out of the
component. So there is nothing for an extractor to be stricter about, which is
what the selector was predicted on and is now the reason it is the fix.
`demoSelector` on an entry is that say, checked with the same `carries()` the
root class is: `card` selects `card-title`, the sub-component its own curated
markup spells and the settings form does not, and takes the canonical card on
both checkouts. It narrows and never widens — a selector no example carries
derives nothing, so the answer keeps the bundled markup and labels it a fallback
rather than reverting to the scaffolding it was written against. `catalog:check`
digests the selected examples for the same reason, and proved it by failing on
`card` alone the moment the selection changed.

## Since then

The four other scaffolding demos are read and left uncurated, because the
selector cannot honestly fix them: `Input.fluid.html` and `Buttons.fluid.html`
name the component nowhere except inside that grid, and
`StatusIndicators.fluid.html` wraps every one of its nine examples in demo
layout. There is no better example to select, so selecting one would only move
which scaffolding is handed over. What they need is a way to say the demo shows
the component nowhere copyable and keep the curated markup — the fallback this
decision already **Assumed** for a template with no example at all, reached by a
judgment rather than by the count being zero. That is a second field and it is
not this one, so the queue carries it rather than `demoSelector` being stretched
to mean two things. The first half of **Wrong if** — state that exists only at
runtime — is untried either way.

The field is `demoDerives`, and the four are curated. It is a second field
rather than a `demoSelector` of `false`: one value that both picks an example
and says there is none is two rules read off one place, and the entry stops
saying which was meant. The permissive extractor is dismissed for the reason
above — every one of these examples carries the root class correctly, so there
is nothing to be strict about — and so is deriving nothing wherever a known
scaffolding class appears, which is the same extractor wearing a blocklist and
would decide by a pattern what the index decides by reading. An entry that
derives nothing is not read at all rather than read and filtered, so the demo is
not among its `sourceFiles`.

`dropdown` was checked separately, and it is a suppression rather than a
trimming question: its three examples on 14.3 and main are the popover dropdown
inside an inline-styled flex row, a legacy `data-bs-toggle` variant, and a
submenu in the same row — and the curated markup is already the first of them
without the wrapper, so there is nothing a trimming rule would win. `button` is
not one of the four and never was: `Buttons.fluid.html` opens with a bare
`<button class="btn btn-default">Button title</button>`, which is the entry's
own markup. The four are `button-group` — the `btn-group` half of that file —
`dropdown`, `input` and `status-indicator`, confirmed by reading all 25 entries
against both checkouts again. `catalog:check` digests the whole file for them,
because what a rewrite can change there is whether the judgment still stands.

## Since then

Re-read on 2026-08-22. `bin/cli catalog:check` reports every demo as its entry
recorded it, the four `demoDerives` entries included, so the second half of
**Wrong if** stays where the two sections above left it. The first half is
untried for the reason it always was: state that exists only at runtime needs an
installation to look for, and a checkout cannot show it.
