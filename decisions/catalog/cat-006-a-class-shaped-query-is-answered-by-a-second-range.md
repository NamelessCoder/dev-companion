---
id: D-CAT-006
title: 'A class-shaped query is answered by a second range'
date: 2026-08-21
status: open
coveredBy:
  - CatalogTest::aClassIsAnsweredOnAVersionItsOwnEntryIsWithheldOn
  - CatalogTest::aQueryThatNamesNoClassOfAWithheldEntryIsAnsweredWithNothing
  - CatalogTest::theClassListReachesAtLeastAsFarBackAsTheEntryItBelongsTo
---

# D-CAT-006 — A class-shaped query is answered by a second range

**A catalog entry carries a second derived range for its class list, and answers
a class the query names below the version the entry is bound on.**

`D-CAT-001` binds an entry whole, so one custom property that arrived late
withholds the classes beside it. A caller that borrowed a single backend class
into its own stylesheet is then told nothing, and what it wanted to know was
whether that class is still there.

## Evidence

- Derived on 2026-08-21 against `.checkouts/` 12.4, 13.4, 14.3 and main. Over
  the class list alone, 4 of 26 entries reach further back than the entry does:
  card and pagination to every covered version, panel and table to v13.
- `feedback/2026-08-19-090231` asked about `table-fit` on v12, and that is not
  among them. The class is written in `_table.scss` on 12.4 and `table-striped`,
  `table-hover`, `table-sm` and `table-selected` are not, so the list binds at
  v13 and a v12 caller is still told nothing.
- A range per class would answer it. It would also record 120 class-to-version
  pairs across 21 entries, re-derived by hand on every core release, because
  `bin/cli catalog:check` reports a binding rather than writing one.

## Decided

- The second range covers the class list, not one class. It is the reading
  `bin/cli catalog:check` already does over fewer names, so one command derives
  both and neither can drift from what a checkout says.
- Only the classes the query named outright come back. The whole list would be
  the entry again minus the custom properties that withheld it, which is the one
  thing `D-CAT-001` decided not to hand over.
- The answer is a block and a field of its own — `coveredClasses` — carrying the
  class, the range and the Sass file the core writes it in. Beside the component
  it would read as a component; carrying no markup it cannot be pasted.
- The entry's own binding is untouched, so a paste-shaped query on an entry
  bound above the caller's major is still withheld.
- An entry whose contract came from the installation has no second range. Those
  packages are the reading, so a class they do not carry is absent rather than
  unverified.

## Assumed

- A caller that names a class is asking whether the class exists, and a caller
  that names a component is asking to paste it. The query is the only evidence
  for which of the two it is.
- The core's own Sass is the evidence, as in `D-CAT-001`. A class Bootstrap
  ships and the core never spells out reads as absent, and that is what keeps
  `table-fit` from being answered on v12 — not anything decided here.

## Wrong if

- A session composes markup out of a class answer. The block says the entry is
  withheld and hands over nothing to copy, so what would show this wrong is a
  feedback reporting a component built from one class name.
- The two ranges keep agreeing. Today the second one buys 4 entries and costs a
  field on 21, and a core release that lets the custom properties arrive with
  their classes would take even that away.

## Since then

The first **Assumed** was refuted on 2026-08-24: a caller naming a class is not
necessarily asking whether it exists. `table-fit` is the wrapper around a
`.table`, so the block answers the name and the range truthfully and still lets
the caller attach it to the wrong element — which is what
`feedback/2026-08-19-090231` did. The entry's own shape carries the same
conflation, `table-fit` sitting in `modifiers` beside a class that goes on the
table itself. Nothing is decided about that here; the repair, if there is one,
is about where a class sits.

The range this entry decided was replaced the same day: `D-CAT-008` derives the
class ranges from a file the core commits, so the hand-maintenance objection
fell rather than the reasoning. The rest stands.
