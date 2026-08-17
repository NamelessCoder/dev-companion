# Key the todo:home stub on the command rather than on the line

**Serves:** tests/
**Priority:** normal

`TodoHomeTest::gitThatAnswers()` matches a case's override with
`str_contains($line, $carries)` against the whole command line, and that line
carries `Paths::root()`. So a checkout whose directory name contains a key
answers the wrong call: in
`.worktrees/nothing-enumerates-what-a-composer-install` the two cases keyed on
`composer` fire on `git -C <worktree> rev-parse --abbrev-ref HEAD`, which then
reports `FAILURES!` as the branch name, and
`aRedSuiteStopsBeforeTheMergeAndKeepsTheWorktree` and
`theSuiteIsAskedAfterTheRebaseRatherThanBeforeIt` fail before the command
reaches the step they are about. Both were red on 2026-08-18 with nothing in the
worktree changed, so `composer ci` is red there for every commit. Match on the
command rather than on the line — the executable and its arguments, with the
`-C <path>` the caller passes left out of what is compared.
