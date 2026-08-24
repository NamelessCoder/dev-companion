# The re-ask reaches a session that called once and not one that called nothing

**Serves:** feedback/2026-08-24-133515-a-full-core-patch-session-ran-green-with-zero.md, D-SKL-062
**Priority:** normal

`D-SKL-062`'s re-ask is built and rides on `TaskGuide::answer()`'s `nextTools`,
so a session that calls nothing never sees it — and one took two of its four
acts on 2026-08-24, writing a core patch with a new functional test case and
several runs of `Build/Scripts/runTests.sh`. The `instructions` are the channel
that reaches such a session, and they were dropped for room: 2028 characters of
the 2048 `R-ANS-013` holds, re-measured that day.

Weigh binding the imperative already in `instructions.start` to the acts as well
as to the opening, which is a rewrite of a sentence rather than a second one,
and say what gives way where it does not fit. Settle in the same step whether
the first edit to code the package ships joins the four acts — the feedback
names it and `D-SKL-062` does not carry it. Hold the result against
`ScopeTest::theInstructionsFitWhatAClientKeeps` and against `D-AUD-012`'s first
**Wrong if**, which is what a second run of the counted shape would answer.
