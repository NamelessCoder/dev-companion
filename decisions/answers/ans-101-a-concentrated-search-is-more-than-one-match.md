---
id: D-ANS-101
title: A concentrated search is more than one match
date: 2026-08-24
status: open
---

# D-ANS-101 — A concentrated search is more than one match

**`typo3_rule_lookup` hands over a whole page only where more than one section
matched, and the record it returns carries the score and the coverage that
search measured.**

[`D-ANS-076`](ans-076-a-search-matching-one-page-answers-with-the-page.md) made
a search whose hits sit in one page an answer of that page. Its premise is that
such a search has established which page answers the task, and a search with one
hit has established that one word landed somewhere.

## Evidence

- Re-run on 2026-08-24 through `RuleLookup::answer()`: `query="signed-off-by"`,
  `targetVersion="15.0"` answers `any/testing/proving-a-condition` whole — six
  kilobytes on proving a TypoScript condition — with
  `matchedHeadings: ["Which URL Is Requested"]`. That is what
  `feedback/2026-08-24-110851` reported, unchanged.
- One term reached it. `TermSearch::terms("signed-off-by")` is `["signed"]`:
  `meaningful()` keeps the hyphen as a word character, so the trailer is one
  word, and `stem()` cuts a word over six characters to six.
- One section of the corpus's 124 carries a word starting `signed` — "a browser
  tab that is signed in", under `Which URL Is Requested`. Its weight is
  `log(124/1)`, 4.82, and a one-term query is covered whole or not at all, so
  `Documents::MIN_COVERAGE` has nothing to cut.
- The number the session ranked the answer by is not a number.
  `Documents::search()` scores that match 48, and `Prose::pageRecords()` returns
  `'score' => 0` and `'coverage' => 1.0` as constants for every whole-page
  answer, the `documentId` path included. Reading `score 0` as "nothing matched"
  was right by accident.
- The two constants disagree, and the wrong one is not the one that read as
  wrong. `coverage: 1.0` asserts that the page covers the whole query, and a
  client validating the declared `outputSchema()` may act on it.

## Decided

- A floor on how many sections matched, not on the score. Score and coverage are
  already what `search()` keeps a match by, and what fails here is a page handed
  over on the evidence of one word. What the floor is is measured by the work,
  against `Documents::topics()`, which says how many of the corpus's own
  subjects reach exactly one section.
- The measured score and coverage go into the page record. A field the schema
  declares is a claim, and a constant standing in for a measurement is the one
  kind of wrong nothing downstream detects.
- Both in one change. Reporting the measurement without the floor answers this
  query with `score: 48, coverage: 1.0` on the same unrelated page, which
  asserts a strong match where the constant only failed to deny one.
- Below the floor the answer is the cut, with the offer to read the page whole
  that `Prose::sections()` already carries. Nothing is withheld; the page stops
  being pushed.
- What the `documentId` path reports is part of the work rather than settled
  here. A caller who named the page was matched against nothing, and `coverage`
  and `score` are required by the declared schema — so a nullable pair is a
  contract change, and the alternative is a record saying the page was named.
- `D-ANS-076` stands. Two thirds of queries reach more than one page and are
  untouched, and the round trip it removed stays removed; what moves is the
  bottom of its range.

## Assumed

- That a one-hit search is rare enough for the floor to cost little. Not
  measured here; `Documents::topics()` and `bin/cli tools:record` are where it
  is.
- That the hyphen and the six-character stem are right elsewhere.
  `signed-off-by` is one word to `meaningful()` by the same rule that keeps
  `f:if` and `EXT:core` whole, and this entry changes what is done with a thin
  match rather than how a query is cut.

## Wrong if

- A query with one strong match is answered with a cut and the caller pays the
  second call `D-ANS-076` removed. Then the floor is on the wrong quantity and
  the score is what should carry it.
- A caller acts on the coverage of a whole-page answer and it is still not that
  search's. Then the record is built in one more place than this reached.
- The trailer query still answers with an unrelated page once the floor is in.
  Then the miss answer is what needed the work, and
  [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md) is where
  it goes.
- The floor hides the answer to a rare exact term — a class name occurring in
  one section and nowhere else. A count cannot tell that from this query, and a
  session reporting it says the score had to carry the decision after all.
