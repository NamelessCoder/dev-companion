---
id: D-CAT-1
date: 2026-07-29
status: open
---

# D-CAT-1 — A catalog entry is bound whole, and the binding is derived

**`since`/`until` sits on the whole catalog entry rather than on its fields, and
is derived by `bin/cli catalog:check` rather than judged.**

An entry that does not hold on the stated version is withheld rather than
qualified.

The architecture hints bind one statement at a time. A component entry has no
statements: it is markup, a class list and a custom-property contract that were
read off one revision together, and a caller pastes them together.


- **Since then** the first half of **Wrong if** is closed by the command that
  derives the binding. Each entry records in `markupDigests` what its demo said
  on every covered checkout that has one, and `bin/cli catalog:check` re-reads
  them: the styleguide examples carrying the component, or the whole file where
  a demo wraps none — `Panels.fluid.html` and `RecordSearchBox.fluid.html` are
  pages about one component rather than galleries. A demo rewritten around
  identical class names passes the binding and fails the digest, which is the
  case this could not see. Nothing is written by the check, because the new
  digest is only true once somebody has reread the entry against that demo.
  Seven demos render the component through a ViewHelper and name it nowhere, and
  three entries name no demo at all: for those ten the digest holds the demo
  rather than the markup, and the command counts them so the number stays
  visible. The second half is untouched — no entry has a hole today, and
  `derivedSince()` answers such a range with its newest unbroken run rather than
  with nothing.

## Evidence

- Run against 12.4, 13.4, 14.3 and main. 22 of 25 entries bind: 13 from v13,
  where the `--typo3-*` custom-property contract arrives, and 9 from v14.
  Avatar, the infobox ViewHelper and the record search box hold everywhere.

## Decided

- `since`/`until` sits on the entry rather than on its fields. The finer split
  — this variant since v14, that property since v13 — is what the Sass sources
  already say, and it would put four ranges in an answer whose reader wants one
  question answered: can I paste this.
- An entry that does not hold on the stated version is withheld, not qualified.
  A qualified class name still ends up in a stylesheet, and a custom property
  that does not exist fails in a browser without an error.
- The binding is derived by `bin/cli catalog:check`, not judged. An entry holds
  on a version when every Sass file it names exists there and every class and
  custom property it names that the newest covered version writes in its Sass
  is written there too — plus, for a custom element, its tag name in the
  TypeScript that defines it.

## Assumed

- What the core writes in its own Sass is the evidence. Classes Bootstrap
  generates from a state map — `btn-secondary`, `badge-secondary`,
  `callout-warning` — appear in no stylesheet on any covered version and are
  therefore not evidence for or against; including the TypeScript corpus in
  that check made an incidental v14 usage of `btn-secondary` bind Buttons to
  v14.

## Wrong if

- An entry's markup changed while every name in it stayed — the derivation
  reads names, not structure, and would call that unchanged. Also a range with
  a hole in it, which `derivedSince()` reports as no binding at all, on the
  grounds that such an entry needs splitting rather than a number.
