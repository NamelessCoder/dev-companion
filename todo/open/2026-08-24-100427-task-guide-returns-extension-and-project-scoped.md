# The brief ranks a hint by whose repository it is for

**Serves:** feedback/2026-08-24-100427-task-guide-returns-extension-and-project-scoped.md, D-ANS-097, R-ANS-033
**Priority:** normal

Judged on 2026-08-24 by re-running the call: the diagnosis and both measurements
are in `D-ANS-097`, what must hold is `R-ANS-033`, and the feedback's
weak-intent half is answered and trimmed off. What is left is the ranking.
Measure what putting a hint whose declared scope is the group's above one
declared for another repository does to the order, and put the tier where the
group's scope is known — `TaskGuide` has it, `Hints::find()` would have to place
the paths itself and would move `typo3_hint_lookup`'s order with it. The two
calls `D-ANS-097` records are the cases, a hint the order moves down stays named
in `omittedHints` rather than dropped, and `bin/cli hints:coverage`
byte-identical before and after is what says no hint became unreachable. Then
hold it with a case per direction in `HintsTest` carrying
`#[Decision('D-ANS-097')]` and `#[Requirement('R-ANS-033')]`, and archive the
feedback in the same commit.
