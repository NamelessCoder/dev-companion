# A symptom query reaches the hint that explains it from another domain

**Serves:** feedback/2026-08-17-212010-the-knowledge-is-indexed-by-subject-and-a.md
**Priority:** normal
**Branch:** todo/a-symptom-query-reaches-the-hint-that-explains-it
**Claimed:** 2026-08-17

Measure what the domain gate costs a query written as a symptom, then close it.
`bin/cli hints:probe "the content elements render in reverse order"` returns
`content-elements` and not `datahandler-placement`, whose `appliesTo` carries
"reverse order" verbatim: `Domains::detect()` reads "content element" as Fluid
and TypoScript, and `Hints::find()` builds its candidates from the selected
domains alone. Run the sweep
`HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay` holds with an
exact `appliesTo` phrase let past the gate, and report what that does to its
recall and to what the queries return. The `task` parameter of
`typo3_hint_lookup` then says a symptom is a query it takes, in the words the
measurement supports. `D-ANS-081` is the judgement this came out of and carries
the rest of the evidence.
