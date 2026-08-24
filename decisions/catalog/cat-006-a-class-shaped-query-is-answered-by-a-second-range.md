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

The first **Assumed** was refuted on 2026-08-24, reviewing the asset build
workflow that routes to this answer. A caller naming a class is not necessarily
asking whether it exists: the `table-fit` caller was asking where it goes, and
the class-shaped answer cannot say. The maintainer put it as the class not being
helpful without the rest of the markup.

`table-fit` is the wrapper `div` around a `.table`, which the entry's own markup
shows and its class list does not. So the block answers the name and the range
truthfully and still leaves the caller able to attach it to the wrong element —
which is what `feedback/2026-08-19-090231` did, and what a hit here would have
waved through.

The entry's shape carries the same conflation one level up. `table-fit` sits in
`modifiers` beside `table-striped`, and the markup puts one on the wrapper and
the other on the table. Nothing distinguishes a class that goes on the root
element from one that goes around it, so a caller reading the modifier list is
invited to make that mistake before any binding is reached.

Nothing is decided here. What is recorded is that the query is not the evidence
this entry assumed it was, and that the repair — if there is one — is about
where a class sits rather than about which versions carry it.

The range this entry decided was replaced on 2026-08-24. **The second range
covers the class list, not one class** was taken because 120 class-to-version
pairs would be re-derived by hand on every core release; `D-CAT-008` derives
them from a file the core commits on every branch, so the objection fell rather
than the reasoning. What that buys is measured there: 17 of 26 entries hold
classes whose ranges differ, and `table-fit` is answered on 12.4 where the
aggregate withheld it — the caller this entry was written for.

The rest of the entry stands. Only the classes the query named come back, they
come back as a block of their own, and they carry nothing to paste.
