# Decide what an id call's hint index becomes

**Serves:** feedback/2026-08-17-212300-availablehints-is-78-percent-of-everything-hint.md
**Priority:** normal
**Waiting on:** whether `availableHints` is suppressed on a call that names an
    id, bounded there, ranked there, or left as it is. It reverses part of
    `D-ANS-075` either way, it changes what every answer of `typo3_hint_lookup`
    carries, and the measurement does not settle it — so it is a question about
    what is wanted rather than one this repository can read.

Judged on 2026-08-18 as step 5, contradicts a decision. The reading is written
into
[`D-ANS-075`](../../decisions/answers/ans-075-the-hint-index-is-ordered-by-the-rank-the-matcher-already-computed.md)
as a **Since then**, and the numbers below come from there rather than from the
feedback.

`normal` because three reports from two sessions in two directories say it:
`2026-08-17-212300` prices the index, `2026-08-17-205945` shows it unread, and
`2026-08-10-182451` reported the same subject from the other side a week
earlier. It is not `high` because nothing is wrong while it waits — every answer
is correct, and what it costs is paid in tokens.

## What the re-measurement established

Replaying the session's 21 id calls at `targetVersion=14.3`: the index is 67.9%
of the text those answers carry and 74.5% of the structured half. Every one of
those answers listed at least one of the three ids the same session then went to
the filesystem for, mostly between 4th and 19th of a list of 25 to 112 — so the
head of the list is where they stood, and the head is not what was read.

The ordering `D-ANS-075` decided reaches no id call. `available()` orders the
candidates `find()` scored, and an id call scores nothing — it returns through
`index()`, which reads the corpus by file. An id call's index is every hint in
the returned hint's own domains: 19 to 113 entries, 90 in ten of the twenty-one.

## The four shapes, and what each costs

1. **Suppress it on an id call.** Recovers the two thirds those 21 answers spent
   on it. `Hints::find()` returns `availableHints` empty for the id path, and
   the copy and `outputSchema()` drop the sentence about what stands beside the
   hint. Costs the discovery route nothing that is on record — no filed session
   attributes a fetched id to an id call's index — and costs it something nobody
   has measured.
2. **Bound it there.** Keeps a pointer at a fraction of the size. The first *n*
   of a file-order list is an accident, so this buys the saving and keeps the
   half of the problem `D-ANS-075` was written against.
3. **Rank it there, then bound it.** Score the domain's hints against the
   returned hint's own words, so "closest first" becomes true on the path that
   claims it. The most work, and the only shape that makes the index on an id
   call an answer rather than a catalogue.
4. **Leave it.** The cost stands and the fourth **Wrong if** of `D-KNW-055`
   stays fired.

**Recommended: 1.** It is the change the measurement supports, the smallest
surface, and the only one that does not rest on an unmeasured belief about what
a caller reads. What argues against it is that it is irreversible in evidence
terms — once the index is gone from the id path, no later session can report
having used it.

## What the work is once it is answered

Whichever shape is chosen, `outputSchema()`'s `availableHints` description says
"closest first", which is false on the id path today, and the answer's own copy
says "The hints alongside it, requestable by id". Both are part of this change
rather than a card of their own. `HintsTest` is where the new behaviour is
asserted, beside the five methods `D-ANS-075` names.

The feedback stays open until the commit that implements the answer archives it.
