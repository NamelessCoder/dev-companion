# A suite that stages the working tree is offered marked rather than withheld

**Serves:** feedback/2026-08-24-100604-suites-are-not-marked-by-whether-they-rewrite.md
**Priority:** normal
**Branch:** todo/suites-are-not-marked-by-whether-they-rewrite
**Claimed:** 2026-08-24

Judged on 2026-08-24 as step 1a with the shape missing beside it, and written up
as `D-ANS-099`, which carries the measurements and what the values owe:
`R-ANS-034` is the rule. Three steps, in this order.

Mark every suite in `knowledge/test-suite-hints.json` by what running it does to
the checkout, in `typo3_project_describe`'s values plus a fourth for the ones
that run git, read off each case body in `.checkouts/` and never by running it.
Declare the field in `Schema::testSuiteRecord()`, print it beside the command in
the text half, and hold it in the tool's contract test — with `build` as the
suite that rewrites tracked files, `lintTypescript` as the one that does not,
and `unit` as the test suite that is `unknown`.

Add `checkGruntClean` as an entry, in the typescript domain, on every covered
major, carrying that mark and what its body does: it deletes the generated
`.js`, rebuilds, and runs `git add *` over the whole working tree.
`checkIsoDatabase` and `checkCharsets` are the other two of the class and open
with `git checkout -- composer.json` and `git checkout -- composer.lock`.

Rewrite the `runTests.sh -h` note in `invocation.notes` to say that it is how a
suite is confirmed to exist on a branch, and that grepping the case label misses
a glob — `build*)` on 13.4 and up, `buildCss)` and `buildJavascript)` on 12.4.

Left to establish before it is written: the feedback's third ask, that the node
suites run in a bare git worktree while the PHP ones need `-s composerInstall`
there first. It is stated in the report as an inference that held once, and
`invocation.preconditions` today says only the half that fails.
