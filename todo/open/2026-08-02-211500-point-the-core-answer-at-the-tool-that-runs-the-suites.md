# Point the core answer at the tool that runs the suites it does not declare

**Serves:** feedback/2026-08-01-114807-session-debrief-precise-transcript-and-tool.md
**Priority:** low

Step 2 of the ladder, on the evidence in
[`D-ANS-031`](../../decisions/answers/ans-031-the-core-answer-names-the-suites-it-does-not-declare-and-not-the-tool-that-runs-them.md):
the commands paragraph in `ProjectScope::text()` tells a core checkout that "the
core's testing suites do not" exist among its declared commands and names nothing
that has them, and a session read it and reached for a `Build/bin/phpunit` the
checkout does not contain. Settle first which tool the sentence should name —
`typo3_test_run_guide` answers with the `runTests.sh` suites and `typo3_script_lookup`
with the core's scripts by task, and `115716` credits the pair rather than either
— by calling both from `/home/benji/projects/typo3-cms` and reading what each
returns for a test question. Then write the clause into that branch of the
paragraph only, since the composer-project branch beside it has no suites to
point at, and extend `ProjectTest::theAnswerNamesTheCommandsThatExistHere`, which
already asserts on "testing suites do not". Decide in the same commit whether
[`R-GUI-003`](../../requirements/guides/gui-003-a-guide-points-at-the-tool-that-performs-the-step.md)
widens from a brief to any answer that names a step, or whether a requirement of
its own carries the project answer — `ScopeTest::theBriefPointsAtTheGuideForTheStepItEndsWith`
holds the first for briefs alone today.
