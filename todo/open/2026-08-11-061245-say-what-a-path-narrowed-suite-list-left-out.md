# Say what a path-narrowed suite list left out, and when it stops holding

**Serves:** feedback/2026-08-10-182435-a-path-scoped-suite-list-goes-stale-when-the.md
**Priority:** normal

In `src/Tool/TestRunGuide.php`, the block that reads "Narrowed to the css and
fluid domain(s) the given paths touch" gains the other half: the domains no
given path reached, how many suites they hold, and that a path landing in one of
them means calling again. Name the domains and count the suites; do not list
them — `D-ANS-074` is the judgement and its boundary. Carry the same in the data
half as a field beside `domains`, which means every path through `answer()` has
to set it, the outside-core early return included, because `outputSchema()` is a
contract clients validate against. `bin/cli tools:index` then rewrites
`documentation/tools/typo3_test_run_guide.md`, and the case belongs beside
`HintsTest::aPathNamedInTheQueryNarrowsTheSuitesAsAnExplicitPathWould`, which
already asserts the Sass-only narrowing this adds the other half to.
