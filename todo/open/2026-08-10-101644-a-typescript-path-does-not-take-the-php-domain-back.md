# a TypeScript path does not take the PHP domain back

**Serves:** feedback/2026-08-10-101644-the-core-s-javascript-unit-test-layer-has-no.md
**Priority:** normal

Judged as `D-KNW-067`: the missing half is written as the `javascript-unit-tests`
hint, this is the routing half. `Domains::detect()` reads `unit test` and the
phrasings beside it as PHP keywords — deliberately, `D-KNW-009` — and nothing
takes that back when every path handed in is TypeScript, so a query about a
`.ts` test selects the PHP domain and every PHPUnit hint becomes a candidate.

The step is a carve-out in `Domains::detect()` of the shape the
`ADMINISTERED_FROM_THE_BACKEND` one already has: the testing keywords do not add
PHP where `fromPaths()` says the paths carry a domain and PHP is not among them.
Free text alone may not narrow it — a negated mention reads like a positive one,
which is what `fromPaths()` exists for.

What has to be shown before it lands is that no scenario gets worse:
`bin/cli hints:coverage` and the ranking assertions in `tests/Unit/HintsTest.php`
are what say so, and the answer selects on the domain, so this reweighs every
query carrying a testing word.

The same feedback carries a second, smaller observation: `typo3_test_run_guide`
with the changed paths largely restates what `typo3_task_guide` already returned
in `checks` and `testSuites`. Judge whether that round trip is worth what it
adds before closing the feedback.
