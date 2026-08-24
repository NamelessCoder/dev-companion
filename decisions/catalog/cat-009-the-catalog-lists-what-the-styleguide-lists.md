---
id: D-CAT-009
title: The catalog lists what the styleguide lists
date: 2026-08-24
status: open
coveredBy:
  - CatalogTest::aComponentNoStyleguidePageDemonstratesIsNotAnswered
  - CatalogTest::anElementIsNotOfferedBeforeItsDemoWroteIt
  - CatalogTest::anElementNoDemoWritesIsNotOffered
  - CatalogTest::anElementTheQueryNamesIsOfferedAsTheWayIn
  - CatalogTest::everyEntryNamesTheActionsThatDemonstrateIt
  - CatalogTest::noEntryAnswersForAMajorItsStyleguideDidNotListItOn
---

# D-CAT-009 — The catalog lists what the styleguide lists

**A component is in the catalog because the styleguide lists it, and the entry
names the actions that demonstrate it.**

`D-CAT-004` selected the index on what the core files as a component — a Sass
partial, a custom element. That is a structural criterion, and the maintainer
named on 2026-08-24 what it admits: some of what the backend styles it keeps to
itself, and nothing in the catalog said which.

## Evidence

Read on 2026-08-24 against `.checkouts/14.3` and the derived files.

- The rule is the maintainer's: what the styleguide lists is public API, and
  what it does not list is not to be used or suggested.
- The listing is `$allowedActions` in the styleguide's `ComponentsController`,
  31 components on 14.3 and 23 on 13.4, in a committed file.
- **The index is wrong in both directions.** Sixteen of the twenty-six entries
  match an action by name. Five entries have no page anywhere — `dropzone`,
  `module`, `note`, `popover`, `recordsearchbox` — and fifteen listed components
  have no entry at all.
- Four of the remaining five are settled by counting what a page writes:
  `btn-group` appears 16 times in the buttons page, `form-check` 177 times in
  checkboxes and 61 in radio, and `typo3-backend-progress-bar` 26 times in
  progress indicators.
- `alert` is settled by the maintainer instead. It appears in no candidate page,
  because the flash messages page writes only its own container and the markup
  comes from a renderer, and the class belongs to that.
- `callout` and `infobox` are two entries on one `rootClass`. The first carries
  eleven classes and no page, the second a page and no classes.
- Thirteen of the 137 custom elements are written literally in a styleguide
  template. A tag survives that reading where a class name does not, because a
  demo builds class names in an `f:for` loop and never builds a tag name.

## Decided

- An entry names the styleguide actions that demonstrate it, as a list. A
  component demonstrated on two pages is one entry naming both, which
  `form-check` is; splitting it would divide one `rootClass` between two
  entries.
- The five entries no page demonstrates come out. What they carried is not
  answered rather than answered with a warning: a marking that says "not public"
  and hands the class over anyway is read as the class.
- `callout` and `infobox` become one entry, under the name the styleguide uses,
  carrying the classes `callout` had.
- The fifteen listed components without an entry are written. Their titles,
  summaries and root classes are drafted from the templates and the Sass and
  corrected by the maintainer; everything else is derived (`D-CAT-008`).
- The custom elements become an answerable dimension of their own, out of
  `component/elements.json`. An element cannot be attached to the wrong node, so
  where one exists it is the answer and the class is the way round it.
- Rejected: keeping the five with a marking. Rejected too: mapping one entry to
  one action, which would leave a caller asking about radio buttons with
  nothing.

## Assumed

- That the listing is the whole of the boundary. A component the core offers and
  never demonstrates is not in it, and nobody has looked for one.
- That `alert` is the flash message markup. The template does not show it and
  the renderer was not read; the maintainer answered it, and reading the flash
  message renderer on a branch is what would confirm it.
- That a tag written in a template is demonstrated. Thirteen came back that way,
  and an element a page instantiates from JavaScript would not.

## Wrong if

- A caller asks about one of the five and the miss costs them. `module` is the
  likeliest: the chrome around a backend module is what a module's own template
  sits in, and a session authoring one has asked before.
- One of the fifteen new entries is wrong in a way the derivation cannot catch —
  a `rootClass` that is not the root, a summary that describes the page rather
  than the component.
- The listing turns out to be a menu rather than a boundary, and something on it
  is internal. Then what the styleguide lists is evidence and not the rule.

## Since then

The third **Wrong if** is partly realised, and it was realised the same day.
Reading the eleven actions no entry covered showed that two of them are not
components: `developerTools` demonstrates the `f:debug` ViewHelper, and
`exception` has no component template at all — the action raises one to show how
it is displayed. So the listing is a menu of pages with a few non-components
among them, and the rule holds with those two counted rather than as an
unqualified equivalence.

Four more of the eleven demonstrate a web component and no class at all —
`comboboxes`, `contentNavigation`, `datetime` and `progressTrackers` — and are
answered from the element dimension rather than as entries. Two were already
covered by an entry under another name: `textarea` writes `.form-control`, which
is `input`, and `tab` writes `.tab-pane`, which `_nav.scss` styles beside the
nav. `notifications` renders the same markup as the flash messages, so `alert`
names both.

What was left was two entries, `form` and `select`, written on 2026-08-24 and
still to be corrected by the maintainer.

The three questions the todo left open were settled on the same reading.

**Listing a component makes its classes answerable**, because the entry is the
unit. Whether one class is demonstrated cannot be read out of a template — a
demo renders through ViewHelpers and web components, and what it does write is
as often its own page furniture — and a marking derived from that reading was
built, measured wrong in both directions, and taken out again. A tag survives
the same reading, which is why the elements carry one and the classes do not.

**Below the oldest major any styleguide ships on, the selection made above it
applies.** The listing cannot be read there at all, and withholding everything
would take the answer away from the caller this catalog was repaired for: a
class that exists on the older major is still answered, and only which
components are catalogued rests on the listing.

**The boundary binds by version and nothing crosses it.** Measured over the
twenty-two entries: every one answers from the major its own listing starts on
or later, and `CatalogTest::noEntryAnswersForAMajorItsStyleguideDidNotListItOn`
holds it from here rather than leaving it measured once.
