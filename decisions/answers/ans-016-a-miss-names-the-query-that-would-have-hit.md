---
id: D-ANS-016
date: 2026-08-02
status: open
---

# D-ANS-016 — A miss names the query that would have hit, not only the reach of each word

**The changelog miss owes the caller a query rather than per-word counts: the
term to ask again with, and the terms that have to go first.**

`typo3_label_lookup` prints the same reach line off the same matcher and ends it
with "ask again with the one that narrows best". `typo3_changelog_lookup` prints
the numbers and stops there.

## Evidence

- `feedback/2026-07-31-194819` re-run on 2026-08-02 from
  `/home/benji/projects/site-new`, against the 3763 changelog entries TYPO3 14.3
  ships. `form set yaml registration deprecated` and
  `form sets discover yaml configuration` both still return nothing, and every
  word of both reaches entries on its own — 358, 213, 17, 29 and 144 for the
  first.
- The sentence the sibling tool already carries would have ended it.
  `query: "yaml"` with no type and no version returns 17 entries, and the first
  two are `14.2 Deprecation: TypoScript-based form YAML registration (#109412)`
  and `14.2 Feature: Auto-discovery of form YAML configurations (#109412)` — the
  two entries the feedback names, in one more call rather than three.
- The rule the feedback asks for is the wrong way round. It wants the word with
  the smallest reach dropped. That word is `yaml` at 17, and it is the one term
  both target entries carry; dropping it loses them.
- No single word can be dropped either. Both queries need two terms removed
  before anything matches — `form yaml registration` reaches the deprecation,
  `form discover yaml` reaches the feature — and no four-term subset of either
  reaches a thing. Marginal counts cannot show that, so "the reach line already
  carries the data to do this" is false.
- Naming the surviving subset outright cost 28 ms and 33 ms over the whole
  changelog: 15 subsets for a five-term query, peeled a level at a time until a
  level hits. The `tag` filter `D-ANS-006` added costs 23 ms for one major's
  deprecations, read off the same file names.

## Decided

- **Step 4 of the ladder, wording.** The tool was called correctly, answered
  honestly and reached the session. What it delivered was data about the query
  rather than a next call, so the session spent three calls arriving at one it
  could have been handed.
- **Queued rather than closed on the spot.** Both halves are `src/`, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line, and the second half is a computation rather than a
  rewrite.
- **The suggestion is rejected as written and taken on what it is after.** The
  smallest reach names the term to keep, not the term to drop, and no marginal
  count answers which drop lets the intersection survive. What is owed is the
  subset itself.
- `R-ANS-006` is where this already stands. "A miss says what there would have
  been to find, and what it names can be asked for outright" is general, and
  what holds it is the hint corpus alone: the changelog miss names five numbers,
  and none of the five can be asked for.
- Recorded against the answer rather than against the search. `D-ANS-006` left
  the matcher right and this leaves it right; what is missing is between the
  empty result and the text.

## Assumed

- That a caller handed one query follows it. Nothing measures which sentence of
  a miss a session acts on, which is the assumption `D-SKL-003` and `D-ANS-010`
  both left open about wording.
- That the peel stays cheap as the changelog grows. It reads file names and
  never opens an entry, so it scales with the count above rather than with the
  files.
- ~~That two words is far enough to peel. Both queries here recovered at that
  depth, and a query needing three has not been seen.~~ Moot on 2026-08-02: what
  was built is one pass with no depth to bound, and one of the twelve queries it
  was run over recovered only at two of five words.

## Wrong if

- A caller follows the offered re-query and gets entries about something else,
  because the surviving subset is the query's vague half rather than its
  subject.
- A miss carrying the sentence is followed by the same trial-and-error in a
  later feedback. Then the next call was not what the session was missing, and
  `D-SKL-003`'s bounds are the whole of the answer.
- The peel is measured against a fuller changelog and costs more than the `tag`
  filter it was priced against. Then it is a second call the caller makes rather
  than something a miss can afford.

## Covered by

- `LabelSearchTest::anEmptyResultNamesTheLargestPartOfTheQueryThatDoesReach`
- `LabelSearchTest::theSubsetThatNarrowsBestComesFirst`
- `PackageSourcesTest::aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit`
- `PackageSourcesTest::aMissNarrowedByAVersionOpensWithTheVersionThatEmptiedIt`
- `PackageSourcesTest::aFilterThatChangedNothingIsNotBlamedForTheMiss`

## Since then

Built on 2026-08-02 as `LabelSearch::largestReachingSubsets()`, and not as the
peel this was priced as. A subset reaches an entry exactly when that entry
carries every word of it, so the largest subsets that reach anything are the
largest sets of words a single entry carries — one pass rather than 15 filter
passes, and no depth to bound. 4 ms over the 3766 entries `.checkouts/14.3` now
ships, against the 28 ms enumerating subsets costs and the 23 ms of the `tag`
filter. The **Assumed** that two words is far enough to peel is therefore moot
rather than confirmed: the pass has no depth, and one of the twelve queries it
was run over recovered only at two of five words.

Every largest subset is named rather than the best of them, which the evidence
above had already decided and the implementation made visible. On
`form set yaml registration deprecated` there are two, they reach one entry
each, and the narrower-first tie-break puts `form set yaml` first — that returns
`10.2 Feature: Unify form setup YAML loading (#84203)`, not the deprecation.
Both named, the caller reads `form yaml registration` beside it and gets #109412
in one call. Re-queried against the same checkout: all four offered subsets
return what the miss said they would.

In `LabelSearch` rather than in `ChangelogLookup`, because it takes the items
and the terms `carryingEvery()` and `perTermCounts()` take and answers with the
same matcher, identifier spellings included. `typo3_label_lookup` does not call
it; what decided the placement is which layer owns matching, not a second
caller.

Not offered where a `tag` was asked for. The peel reads file names and a tag is
inside the file, so a subset counted without the tag would name entries the same
call does not return. The sibling's sentence still ends the miss there, on the
per-term counts — which is also where it lands when no two words of a query meet
in one entry, because those counts are then the whole list of what can be asked
for.

## Since then

The same wording is owed for the filters, and `feedback/2026-08-01-115112` is
what shows it. Re-run on 2026-08-02 from `/home/benji/projects/typo3-cms`,
`query: "GifBuilder placeholder preview thumbnail"` with `version: "15"` returns
nothing and says that "preview" reaches 1 entry. Every number in that sentence
is counted inside `15.0`, and nothing marks it as such. Drop the version and all
four words reach — 4, 10, 28 and 9 — and `image generation` returns the `13.0`
entry the session was after, alone, in one call.

So a caller is handed a reach line that reads as a fact about the changelog and
is a fact about the filter. This entry decided that a miss owes a query rather
than counts; a miss narrowed by `version` or `type` owes the same thing about
the axis it was narrowed on. That the filter is what emptied the answer is the
first sentence, and it is the one the report never got: the session read "only
preview reached one entry", concluded the tool could not reach the entry at all,
and went to `grep`.

What the entry did build is unaffected — `largestReachingSubsets()` runs over
the narrowed set, which is correct, because a subset counted outside the filter
would promise entries the same call does not return. It is the sentence around
the numbers that is missing the filter, not the computation. The other half of
that re-run is `D-ANS-030`, which is about the field the matcher reads rather
than about what the miss says, and both are queued.

## Since then

A second corpus asks for it. `typo3_rule_lookup` reached its no-match answer
only where the hints missed too, so a compound query that matched a hint was
told a boundary emptied it —
[`D-ANS-037`](ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md),
built on 2026-08-03 — and the subsets are what that miss now offers instead.

What the second caller showed the two have in common is the pass and not the
matching. `Search\Subsets` is the one pass, handed the corpus's own `carries()`:
the labels match a substring and an identifier spelling, the prose matches at a
word boundary, and a subset offered on the wrong rule names items the same call
does not return. `LabelSearch::largestReachingSubsets()` is that call for the
entries and keeps the field pair and the matcher this entry priced.

*The count is exact* above holds for the changelog and not for the prose, which
is a property of the gate rather than of the pass: a changelog entry is returned
only where it carries every word, while `Documents::search()` keeps a section
covering half the query's weight, so a subset naming one section returns that
one and whatever else clears the floor. It is a floor there, computed the same
way for every subset.

## Since then

The decision this entry names and does not take is taken, as
[`D-ANS-043`](ans-043-a-miss-is-answered-in-data-and-says-which-corpus-its-silence-belongs-to.md),
and what took it is the first report of the gap from practice.
`feedback/2026-08-03-144349` quotes a miss as its six structured fields —
`matchCount: 0` among them — and states that nothing came back to re-ask with.
The subsets were in the text of that same answer.

The first **Wrong if** did not fire and the offer is what went unread, which is
the distinction the entry could not draw when it was written. Re-run on
2026-08-03 from `/home/benji/projects/typo3-cms`:
`typo3 directory backend entry point` returns nothing and offers
`typo3 backend entry point`, which returns
`13.0 Deprecation: TYPO3 backend entry point script deprecated (#87889)` alone.
That entry is what the session's review turned on, and the session settled it by
grep instead.

So the sentence this entry built is answered on its own terms — the offered
subset was the query that would have hit, on a query nothing here chose. What
`D-ANS-043` adds is the half above it: the offer is carried as data as well, and
a miss whose offer also comes back empty names the corpus to ask instead.

## Since then

One reading carried this entry out and established nothing beyond it, so it is a
line here rather than a section of its own. Judged on 2026-08-22.

- 2026-08-03, the wording the reading above found owed: a miss narrowed by
  `version` or `type` counts its terms a second time over the whole changelog,
  and where a word reaches there and nothing inside the narrowing, the filter is
  what the miss opens with. The counts that stay say where they were taken, and
  the "ask again with the one that narrows best" is dropped there, because the
  call to action is the filter. It costs 48 ms over 3795 entries and a hit never
  reaches it. Unlike the subsets it is offered where a `tag` was asked for: a
  count is what a word reaches, and only a subset promises entries the same call
  would not return.
