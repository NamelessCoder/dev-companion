# Say what cglGit inspected, because from a worktree that is nothing

**Serves:** feedback/2026-08-02-144326-working-inside-a-git-worktree-created-under.md
**Priority:** high

A check this server recommends reports SUCCESS having read no file: run from a
git worktree, `CI=true ./Build/Scripts/runTests.sh -s cglGit` prints
`fatal: not a git repository: (null)`, then
`No PHP files to check, all is well`, then `SUCCESS`. The cause is in the
checkout — `Build/Scripts/cglFixMyCommit.sh:118` takes its file list from
`git diff-tree --no-commit-id --name-only -r HEAD` and treats an empty list as
nothing to fix — and the recommendation is here, in the `checks` array of
`fluid-viewhelpers` in `knowledge/hints/fluid.json` and in every other block,
`knowledge/test-suite-hints.json` and `knowledge/task-intents.json` entry that
names `cglGit`. Reproduce it once in a worktree of
`/home/benji/projects/typo3-cms` to establish whether the worktree is the whole
of the condition or only the case that was hit, then decide between recommending
`-s cgl -n` outright and keeping `cglGit` with its precondition named in the
same line, and run the same test against `checkExtensionScannerRst` and the
other suites whose script asks git for the file list. Hold whatever is decided
with a case that fails if a check string is recommended without its condition,
because a false green is the one failure a reading session cannot see.
