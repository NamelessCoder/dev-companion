# Give seen-it-fail its second half, and name the scratch probe a review may run

**Serves:** feedback/2026-08-07-130037-seen-it-fail-does-not-survive-the-commit-and.md
**Priority:** normal

Two additions to published skills, both reported by sessions that credit the
rule they are extending. In `typo3-core-issue-triage`, put the committed case
beside "a green that ran over no files is not a green": once the change is in a
commit, `git stash` saves nothing and the run that follows measures the patched
tree. A session did exactly that and reported the result to its user as a
without-patch measurement; what should have stopped it was `git stash list`
showing only an unrelated entry. Say what to do instead — a worktree on
`HEAD~1`, or `git revert --no-commit` with a restore after — and that the tree
is confirmed to have changed before the run is believed. It is the same failure
the existing warning covers, wearing a second costume: an operation that
silently did nothing, followed by a result that looks like evidence. In
`typo3-core-patch-review`, name the scratch probe as a legitimate method with
its condition: a review may add a temporary fixture column and run a targeted
suite against it, provided the working tree is restored and the restoration is
verified. One session used it four times and turned three findings from reasoned
into measured, including one no amount of reading would have produced; as
written, "a review may run what cannot change the code" reads as forbidding it.
