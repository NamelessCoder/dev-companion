---
id: D-ANS-075
title: The hint index is ordered by the rank the matcher already computed
date: 2026-08-11
status: open
coveredBy:
  - HintsTest::theIndexNamesWhatTheLimitCutBeforeWhatTheFloorRefused
  - HintsTest::theIdsOfferedAreTheOnesThatMajorHas
  - HintsTest::theIndexIsNotOfferingWhatTheSameAnswerWithheld
  - HintsTest::aMissNamesWhatThereWouldHaveBeenToFind
  - HintsTest::anAnswerStillNamesTheIdsItDidNotReturn
---

# D-ANS-075 — The hint index is ordered by the rank the matcher already computed

**`availableHints` is built from the candidates `find()` has just scored,
closest first, instead of from a second read of the corpus in file order.**

The fourth **Wrong if** of
[`D-KNW-055`](../knowledge/knw-055-the-first-check-a-standalone-extension-gets-is-a-gap.md)
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

## Since then

The first **Wrong if** fired on 2026-08-18, and what it leaves standing is the
length and the place the list is printed in rather than the order.

`feedback/2026-08-17-212300` and `feedback/2026-08-17-205945` are one session
building a TYPO3 v14 sitepackage in `/home/benji/projects/site-demo`. It made 21
`typo3_hint_lookup` calls and every one of them named an id. The first report is
what the index cost it — 78% of everything the tool transferred. The second is
what the index bought it, which was nothing, four times over. Both were
re-measured here on 2026-08-18 by replaying all 21 ids through
`bin/typo3-dev-companion` at `targetVersion=14.3`.

The cost reproduces. The 21 answers carry 172,297 characters of text, of which
the index is 116,962 — 67.9%. Their structured halves carry 208,452, of which
the index is 155,311 — 74.5%. The report measured through a client and against a
corpus four hint corrections older, so its 78% and this 68% are the same
reading.

The index named every id that session went around, and named it near the head.
Each of the 21 answers lists at least one of the three. `datahandler-placement`
is in fifteen of them, at 18th or 19th in thirteen and never below 39th of 113;
the session spent five calls rebuilding its subject from `DataHandler.php`.
`preview-record-variable` is in seven, at 4th of 25, 6th of 37 and 15th of 112,
and six backend previews were written from a neighbouring hint and a third-party
package's templates instead. `fluid-layouts-sections` is in the same seven, at
7th of 25, 10th of 37 and 56th of 112, and cost three calls and a diff of two
rendered pages.

So the **Assumed** that a caller reads the head of a list and not its middle is
measured, and it does not hold: the head is where these three ids stood. The
second **Assumed** is untouched, because an id call has no refused tail to be
worth its lines.

What the ordering decided here reached none of those 21 calls. `available()`
builds its order from the candidates `find()` scored, and an id call scores
nothing — it returns through `index()`, which reads the corpus by file. The
index an id call carries is every hint in the returned hint's own domains: 19 to
113 entries, and 90 in ten of the twenty-one. `outputSchema()` says that list is
"closest first", which is not true of it.

Which of the two the index becomes is therefore open again, in the terms
`D-KNW-055`'s fourth **Wrong if** already set — and its other half, that callers
stop reading them, is what these three positions measure. Suppressing the index
on an id call is what both reports ask for and it reverses this entry's own
**Decided**. The measurement does not settle it: it prices the index and shows
the ids were reachable, and what it cannot say is what the sessions that do use
the index would lose. Nothing on record attributes a fetched id to an id call's
index: `feedback/2026-08-10-182451` names the availableHints of its first call,
which was a query call, and `2026-08-17-205945` names `typo3_task_guide`. The
question goes up on
`todo/waiting/2026-08-17-212300-availablehints-is-78-percent-of-everything-hint.md`,
which prices four shapes and recommends one.

**Answered on 2026-08-18** by the maintainer, and this entry's **Decided** is
reversed on the id path: a call that names an id and matches one carries
`availableHints` empty and `availableHintsWithheld` counting what was left out.
A call that matches by paths or task is untouched, and so is an id that matched
nothing — that list is `R-ANS-006`'s and was never what the measurement priced.

The reading that produced the four shapes had one claim wrong, and the
correction is in the feedback it was read from. This entry says nothing on
record attributes a fetched id to an id call's index. `2026-08-17-212300` says
in its own words that several of the ids it called with "came from
availableHints arrays in earlier answers", and all 21 of that session's calls
named an id — so the earlier answers were id calls, and the index did feed
discovery there. What the same paragraph adds is the bound: "after the second or
third, the caller has the list."

So the suppression is not outright. `availableHints: true` is the way back,
which is the fallback that report offered beside the suppression it asked for,
and it leaves the route open for the session that used it. `R-ANS-030` is the
shape it takes — a bound is asked for, and what was left out is counted either
way — reflected: there the caller asks for less, here for more, and the count is
what makes both askable.

**Measured after the change**, on `id=events-extension-points` at
`targetVersion=14.3`: the answer falls from 8,227 characters of text to 1,565
and from 10,859 of structured payload to 1,843, with 93 neighbours counted
rather than listed.

**Wrong if** a session is handed the count, needs one of those 93, and does not
ask — which would say the index had to be there rather than offered.

