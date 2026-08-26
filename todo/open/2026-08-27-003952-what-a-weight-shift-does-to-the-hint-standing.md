# What a weight shift does to the hint standing first is unmeasured

**Serves:** D-ANS-115
**Priority:** low

`D-ANS-115` swept admission and says so: a hint that was above the coverage
floor and falls below it when the corpus grows by one statement. That is the
cheaper half. A shifted term weight also reorders what stays admitted, and
`HintsTest` asserts a first hit by name in many places — those break while every
hint involved is still returned, and the sweep behind the decision says nothing
about them.

Measure the same way: for each query `HintsTest` writes out, bump one term's
document frequency by one and read which hint stands first afterwards. Then say
whether a first-hit assertion is a claim about the hint or about the rest of the
corpus, and if it is the second, what carries it — `bin/cli hints:probe` prints
the score each hit was ordered by.

The queries reaching `Hints::find()` through a variable or a data provider were
not swept for `D-ANS-115` either, and belong in the same reading.
