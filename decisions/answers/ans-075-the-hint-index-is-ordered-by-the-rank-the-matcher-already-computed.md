---
id: D-ANS-075
date: 2026-08-11
status: open
---

# D-ANS-075 — The hint index is ordered by the rank the matcher already computed

**`availableHints` is built from the candidates `find()` has just scored,
closest first, instead of from a second read of the corpus in file order.**

The fourth **Wrong if** of
[`D-KNW-055`](../knowledge/knw-055-the-first-check-a-standalone-extension-repository-gets-is-a-gap-this-server-owns.md)
fired and disproved the remedy it named. A session stopped reading the index,
and the id it needed was in it — three from the bottom, while the matcher had
just ranked it seventh.

## Evidence

- **The reported call, re-measured on 2026-08-11 at `targetVersion=15`.** The
  query of `feedback/2026-08-10-182451` selects `css`, `fluid` and `typescript`,
  scores 52 candidates, admits 13 and returns 6. `javascript-unit-tests` is rank
  **7 of 13** — `limit=6` cut it by one place — and stands at position **43 of
  46** in `availableHints`. The reporting session spent six calls rebuilding
  that hint's subject from the filesystem.
- **The rank is in hand when the index is built.** `Hints::index()` runs a
  second `load()` and orders what it finds by file, in the same `return` where
  `find()` still holds the sorted `$scored`. Nothing separates a hint the
  matcher ranked seventh from one that answered nothing.
- **The index is two things, and only one of them can be ranked.** Of the 52,
  seven were admitted and cut by the limit, and 39 were refused by the coverage
  floor. The first seven carry a rank the matcher stands behind; the rest carry
  a score it has already judged too low to answer on.
- **The second id of the same report is not this question, and not a gap
  either.** `css-tokens-specificity` is 16th of the 39 refused, tied at 47 with
  `css-light-dark-mode` and `css-rtl-logical-properties`, and stood at position
  5 by file order — a coincidence rather than an answer. The query never asked
  for it, and a query that does gets it: `bin/cli hints:probe` on 2026-08-11
  returns it first and on the curated vocabulary for "css custom property token
  specificity", for "derive a css variable from another instead of hardcoding a
  value", and for the session's own dilemma, "should this fade distance get its
  own custom property or borrow --typo3-spacing".
- **The index offers what the same answer refuses.** A frontend query
  withholding `Backend CSS` as inverted advice was measured on 2026-08-11: the
  answer says the category is withheld and then lists 19 of its hints by id,
  because `index()` filters on `$selected` while the matcher filters the
  candidates. The three `backendOnly` exclusions are listed the same way.
- **Withdrawal removes the only place the id was named.** `D-KNW-055` measured
  the index at 47 of an answer's 126 lines and 3545 of its 13080 bytes, and its
  own reading of the firing is that the count was never the cost.

## Decided

- **Ordered whole, not cut to a band.** The near misses come first, in the
  matcher's own order, and what the floor refused follows by score. Cutting the
  tail was priced and refused: it would have dropped `css-tokens-specificity`
  out of the answer altogether, and that id is half of what the report is about.
- **Built from the candidates rather than filtered afterwards.** The refused
  hints are kept where they are scored instead of being dropped, so the index is
  the list the matcher already worked on. What follows from that is what the
  index stops carrying: a hint withheld for the frontend, one excluded as
  displacing, and one whose statements do not hold on the target are no longer
  offered by id, because none of them was ever an answer this call would give.
- **Flat, with the order said in the copy.** A tier marker in the payload would
  be a second concept for the reader, and the first entry is what a caller acts
  on either way.
- **`index()` is what the id path uses, and nothing else.** It answers where no
  query was matched and no rank exists, which is the one case a corpus read is
  the right source for.
- **The report's own suggestion is not taken.** It asks for the ids whose
  category matches an established domain to be split out from the rest, and
  every entry of this index already matches one — the split would be the whole
  list. What separates them is how far each got, which is what the order now
  says.

## Assumed

- **That a caller reads the head of a list and not its middle.** It is what the
  report describes — the list "is long, uniform, and arrives attached to an
  answer about something else" — and it is why ordering is expected to be worth
  more than the length. Nothing has measured a session against an ordered one.
- **That the refused tail is worth its lines.** It is kept because
  `css-tokens-specificity` was in it and because a miss has nothing else to say,
  not because anybody has read one.

## Wrong if

- A session is handed an ordered index, the id it needed is at the top, and it
  goes to the filesystem anyway. Then the order was never what stopped it, and
  what is left is the length or the place the list is printed in.
- A hint the floor refused turns out to be what a session needed, and it was
  below the seven the limit cut. Then the two tiers are the wrong split and the
  matcher's floor is what has to move.
- The index stops naming a subject a caller then fails to find at all, because
  the candidate filter took it out. Then filtering the index by what the matcher
  refused is narrower than the field's purpose, and the domain read is what it
  should be built from.

## Covered by

- `HintsTest::theIndexNamesWhatTheLimitCutBeforeWhatTheFloorRefused`
- `HintsTest::theIndexIsNotOfferingWhatTheSameAnswerWithheld`
- `HintsTest::aMissNamesWhatThereWouldHaveBeenToFind`
- `HintsTest::anAnswerThatMatchedSomethingElseStillNamesTheIdsItDidNotReturn`
