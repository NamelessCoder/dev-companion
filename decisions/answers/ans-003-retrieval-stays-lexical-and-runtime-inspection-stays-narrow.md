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
and the cause is lexical. Two sessions in different checkouts report it:
`2026-07-31-172753` found `#109438` and `#108345` only because the test suite
printed them, and `2026-07-31-194504` reports six queries returning nothing.
Both reproduce from `.checkouts/14.3` today. `ext_tables.php` and
`ext_emconf.php` reach no entry, while `ext tables extensions` reaches
*ext_tables.php in extensions* and `emconf` reaches *Deprecation of
ext_emconf.php*. `Changelog::entries()` searches the file name and its CamelCase
split, `LabelSearch::terms()` splits the query on whitespace alone, and
`carryingEvery()` asks `str_contains` — so a term carrying `_` or `.` matches
nothing, the entry's own title included. That is a query language the tool does
not implement, which is `R-ANS-004`, and the todo behind it is `390`. On the
live documentation the falsifier did not fire: `2026-08-01-002928` missed the
present *Record objects* page three times, but every query was question-shaped
where the schema asks for short ones, and the two-word `Record API` returns it
third today.

## Confirmed on 2026-08-02

The runtime half fired once, in a recorded run, and what it asks for is narrower
than what was refused. `REVIEW-02` run 5 left the v13 delta-only rule for
`ext_tables.sql` unraised, "since I did not verify each column against the
schema analyzer's TCA-derived output" — the one diagnosis in either run its
project files, its effective configuration and its checkout could not complete.
The boundary is one table's TCA-derived columns,
`Core\Database\Schema\DefaultTcaSchema`, and `E-EXT` has no database, so whether
that class answers without one is what todo `400` settles. Two further sessions
could not complete a diagnosis and neither wanted SQL, a log or a schema:
`2026-07-31-174524` needed the v14 Page module to render an identifier-less
backend layout, and `2026-08-01-002745` needed the preview to render
`{record.header}`. The safe shape comes from the second one, which reached for
all three refused sources and got nothing from any. Its `SELECT` and `INSERT` on
`tt_content` were what the user corrected to DataHandler, the log carried only a
stale entry, and the reflection script it wrote into the webroot was stopped by
the user; the answer came from a manual page.

## Since then

The runtime half was read a sixth time on 2026-08-18, and this is the first
reading where the diagnosis was not completable from the caller's own checkout.
`feedback/2026-08-18-074124` wrote against five core APIs in a package declaring
`^13.4.15 || ^14.3 || 15.*.*@dev` with 14.3.6 installed, and the checkout it had
was at the wrong major: it settled the 13.4 half by curling from
raw.githubusercontent.com and then by installing a second core. So the **Wrong
if** fired in the direction it names, and what the session supplies is not the
boundary this entry refused. What was missing is a branch rather than a reader —
another session the same day answered the same class of question at one
`git show origin/13.4:<path>` per file from a checkout that carried it
(`feedback/2026-08-18-080710`), and a third report of that session asks for the
`doesNotCover` entry standing on this decision to stay exactly as firm as it is
(`feedback/2026-08-18-080743`). The lever moved to `D-VER-007`, which is the
procedure that names the reading and reads no core source here.

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
