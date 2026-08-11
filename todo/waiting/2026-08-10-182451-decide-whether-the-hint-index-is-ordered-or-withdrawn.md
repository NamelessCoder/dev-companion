# Decide whether the hint index is ordered or withdrawn

**Serves:** feedback/2026-08-10-182451-availablehints-ids-i-had-already-been-shown.md
**Priority:** normal
**Waiting on:** whether `availableHints` is ordered by the rank the matcher
    already computes, or withdrawn to the miss it was built for. `D-KNW-055`
    names the second as what its **Wrong if** firing would mean, and the firing
    measured for the first. Both change `src/` and what every answer of
    `typo3_hint_lookup` carries, which is what puts it past a judging run.
    Judged 2026-08-11 into `D-KNW-055`.

Step 5: the fourth **Wrong if** of `D-KNW-055` fired, and the measurement is in
that entry rather than repeated here. The short of it is that the session was
shown `javascript-unit-tests` at position 43 of 46 while the matcher had just
ranked it seventh of 52 and `limit=6` had cut it by one place — `Hints::index()`
re-reads the corpus in file order and discards the rank `Hints::find()` holds at
that moment. Priority is `normal` because a second session hit the same shape:
`feedback/2026-08-07-132426`, read in `D-ANS-060`, had the two ids it wanted in
`availableHints` and fetched neither.

The other half of the feedback is not this question and does not wait on it.
`css-tokens-specificity` sits sixteenth of the 39 hints the coverage floor
rejected, tied with two others, so no ordering would have raised it and the gap
is what the query's words reach — a matcher and vocabulary reading, which needs
a core checkout rather than an answer.
