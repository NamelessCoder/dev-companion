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
- The sentence the sibling tool already carries would have ended it. `query:
  "yaml"` with no type and no version returns 17 entries, and the first two are
  `14.2 Deprecation: TypoScript-based form YAML registration (#109412)` and
  `14.2 Feature: Auto-discovery of form YAML configurations (#109412)` — the two
  entries the feedback names, in one more call rather than three.
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
  [judging.md](../../documentation/feedback/judging.md) puts on the far side of
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
- That two words is far enough to peel. Both queries here recovered at that
  depth, and a query needing three has not been seen.

## Wrong if

- A caller follows the offered re-query and gets entries about something else,
  because the surviving subset is the query's vague half rather than its subject.
- A miss carrying the sentence is followed by the same trial-and-error in a
  later feedback. Then the next call was not what the session was missing, and
  `D-SKL-003`'s bounds are the whole of the answer.
- The peel is measured against a fuller changelog and costs more than the `tag`
  filter it was priced against. Then it is a second call the caller makes rather
  than something a miss can afford.
