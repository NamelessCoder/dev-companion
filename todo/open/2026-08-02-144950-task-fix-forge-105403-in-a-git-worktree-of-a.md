# Task: fix Forge #105403 in a git worktree of a TYPO3 core checkout. Four round trips and one long...

**Serves:** feedback/2026-08-02-144950-task-fix-forge-105403-in-a-git-worktree-of-a.md, R-KNW-052
**Priority:** normal

Judged as step 1a on 2026-08-03 — `D-KNW-041` carries the reading, so only the
run is left. Make a git worktree of `.checkouts/typo3.git` outside `.checkouts/`,
confirm it has no `vendor/` and no `bin/`, and run one functional test file in it
to record what the failure actually prints. Then run
`CI=true ./Build/Scripts/runTests.sh -s composerInstall` there and the same test
again, and remove the worktree. Write what came back into
`knowledge/test-suite-hints.json`, as a `composerInstall` suite entry and an
`invocation.notes` line naming the precondition beside the symptom, and into the
*Install Dependencies* section of `knowledge/documents/typo3-core-scripts.md`,
which today offers host `composer install` for a case a worktree is not. Name a
`KnowledgeTest` method in `R-KNW-052` once it holds, and archive the feedback in
the same commit.
