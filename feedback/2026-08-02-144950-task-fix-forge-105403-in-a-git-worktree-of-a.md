---
date: 2026-08-02T14:49:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_project_scope
directory: /home/benji/projects/typo3-cms
---

# Task: fix Forge #105403 in a git worktree of a TYPO3 core checkout. Four round trips and one long...

## Observation

Task: fix Forge #105403 in a git worktree of a TYPO3 core checkout. Four round trips and one long install were spent before a single test could run, on something nothing here warns about.

A fresh git worktree has no vendor/ and no bin/, because they are gitignored and live only in the original checkout. The first test run failed with:

  /usr/local/bin/docker-php-entrypoint: exec: line 9: bin/phpunit: not found
  Result of functional ... FAILURE

The obvious repair is wrong. I symlinked vendor and bin to the main checkout, and the run failed identically, because runTests.sh sets CORE_ROOT="${PWD}" and bind-mounts only "-v ${CORE_ROOT}:${CORE_ROOT}" into the container. A symlink whose target sits outside that mount cannot resolve inside it — and since a worktree under .claude/worktrees/ is a subdirectory of the main checkout, the target is a parent path that is not mounted. A relative symlink escapes the mount just the same.

What works is running the install inside the worktree: CI=true ./Build/Scripts/runTests.sh -s composerInstall. That succeeded and every suite worked from then on.

Round trips this cost: first failing run, symlink attempt, second failing run, composerInstall, then the real run. The failure message points at phpunit and says nothing about the worktree, so the cause is not visible from the symptom.

This is worth carrying because the environment I was given had already placed me in a worktree, and the user asked for one explicitly mid-session. Anyone following that pattern hits this on their first test run.

## Query

CI=true ./Build/Scripts/runTests.sh -s functional -- typo3/sysext/fluid/Tests/Functional/ViewHelpers/ImageViewHelperTest.php, run from a freshly created git worktree at .claude/worktrees/issue-105403-fimage-cachebusting

## Suggestion

State it wherever runTests.sh is recommended, ideally as a precondition in typo3_test_run_guide: a git worktree of a core checkout needs its own dependencies before any suite runs — CI=true ./Build/Scripts/runTests.sh -s composerInstall — because vendor/ and bin/ are gitignored and therefore absent, and symlinking them from the main checkout does not work since runTests.sh bind-mounts only CORE_ROOT into the container. Naming the symptom alongside it ("exec: bin/phpunit: not found") would let a session recognise it from the error rather than from the diagnosis.
