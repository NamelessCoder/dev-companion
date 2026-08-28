---
id: D-ANS-003
title: Retrieval stays lexical and runtime inspection stays narrow
date: 2026-07-30
status: confirmed
coveredBy:
  - StructureTest::retrievalIsLexicalAndNothingHereOpensADatabase
---

# D-ANS-003 — Retrieval stays lexical and runtime inspection stays narrow

**No embedding dependency, no semantic index, and no generic SQL, log or
database-schema tool: what version, audience, binding and source decide is not a
gap a semantic match would close.**

The live-documentation source created the point at which the remaining search
and inspection gaps could be measured instead of anticipated.

## Evidence

- `bin/cli hints:coverage` finds every one of the 61 hints from its own title.
  Seven of 25 scenario prompts reach no architecture hint: `CORE-06`, `META-01`,
  `META-04`, `META-05`, `EXT-01`, `EXT-05`, `SITE-03`. They are respectively
  version spread, orientation, structured-only output, installation, an upgrade,
  the explicit testing boundary and effective runtime configuration. Their
  owning route is not an architecture hint, so a semantic match would make the
  report look fuller without answering them better.

## Decided

- No embedding dependency or semantic index is added. The concrete live-docs
  ranking defect was lexical — separated words tied a precise multi-word title —
  and is fixed by weighting adjacent query terms, guarded by
  `DocumentationTest`. Semantic retrieval may nominate candidates in future, but
  version, audience, binding and source still decide what can be returned.
- No generic SQL, log or database-schema tool is added. No feedback feedback or
  new scenario needed one after live documentation and the existing installation
  diagnostics were available. A runtime tool starts with the session that could
  not finish without it, not with parity against another server.

## Wrong if

- ~~Real queries repeatedly miss a present section after short English
  alternatives.~~ Fired on 2026-08-02 on the changelog, where the cause was
  tokenisation. The miss it names is one two causes share, so it says nothing
  about whether retrieval is what has to change.
- A scenario's diagnosis cannot be completed from project files, effective
  configuration and the caller's own checkout. Record that session; it supplies
  both the tool boundary and the safe result shape.

## Confirmed on 2026-08-02

The retrieval half fired, on the changelog rather than on the prose sections,
and the cause is lexical. Two sessions in different checkouts report it, and
both reproduce: two file names reach no entry, while the same names split into
words reach the entries about them. The search reads the file name and its
CamelCase split, so a name written as one token matches nothing.

## Confirmed on 2026-08-02

The runtime half fired once in a recorded run, and what it asks for is narrower
than what was refused: a review left one rule unraised because it had not
verified each column against the schema analyzer's derived output — the one
diagnosis in either run its files, its configuration and its checkout could not
complete. The boundary is one table's derived columns, and the environment has
no database, so whether that class answers without one is what a card settles.

## Since then

The runtime half was read a sixth time on 2026-08-18, and this is the first
reading where the diagnosis was not completable from the caller's own checkout:
a package declaring three majors with one installed, and the checkout it had was
at the wrong one — it settled the other half by curling a raw file and then by
installing a second core.

So the **Wrong if** fired in the direction it names, and what the session
supplies is not the boundary this entry refused: what was missing is a branch
rather than a reader.

## Confirmed on 2026-08-22

Eight readings held the decision and changed nothing in it. Six are the runtime
half read and answered the other way: the diagnosis completed from the caller's
own checkout, and what was missing was a sentence rather than a source. A
detector was declined because it would key on the wrong signal — nothing
deprecated the API, and what makes an entry dead is that the path it names is
gone. What over-claimed was narrowed instead, and `R-ANS-012` holds the files
that register by running.

The retrieval half was measured on the prose corpus and the ranking was right:
the page was reachable, and what decided each query was a word with nothing to
do with the subject.
