# Settle what a core-shaped answer under the `project` profile may name

**Serves:** R-SCO-7, decisions/
**Waiting on:** does `R-SCO-7` stand as written now that its cost is visible?
    In `E-SITE` the profile is `project` and `typo3_test_run_guide` is not in
    the tool list, but a core-shaped task is still answered as core work, and
    that answer sends the caller to it — twice on a patch task, six times on a
    test task. Trimming those sentences meets the requirement's letter and
    leaves a person genuinely writing a core patch with no pointer to the
    targeted `runTests.sh` invocation at all. Saying instead that the tool is
    not in this list and that `TYPO3_MCP_PROFILE=all` offers it keeps the
    pointer and still names a tool the client cannot call, which is the
    sentence `R-SCO-7` forbids. Which of the two is wanted, or is the
    requirement's sentence the one to narrow?

The reading is done and is recorded on `D-AUD-2` in its **Corrected on** line
of 2026-08-02, which is also where the numbers are. `Scope::offered()` already
filters `doesNotCover`, `checkoutDiscovery` and `routing` against
`Profile::omitted()`; `TaskGuide` reads `Scope::read()` instead and filters by
`Scope::isCoreOnly()`, which is the audience of the task and not the profile.
The three sources that leak are `knowledge/architecture-hints/php.json`, two
entries in `knowledge/server-scope.json` and `src/Tool/TaskGuide.php:502`. Once
the shape is answered, the assertion goes next to
`ProfileTest::theScopeNeverPointsAtAToolThisProfileDoesNotOffer`, which holds
the half that works, and `R-SCO-7` goes back to `held`. Do not re-derive
whether the rules arrive: they do, through the task guide, the commit message
guide and the unfiltered `typo3://core` resources, and `D-AUD-2` says so.
