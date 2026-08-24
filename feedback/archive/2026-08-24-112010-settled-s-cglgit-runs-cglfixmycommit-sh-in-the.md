---
date: 2026-08-24T11:20:10+00:00
category: idea
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide, typo3-core-patch-development, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# Settled: -s cglGit runs cglFixMyCommit.sh in the container, so recommend the suite and drop the rest

## Observation

Task: same session. This settles the cglFixMyCommit.sh thread and replaces the two corrections I filed at 11:16:37 and 11:17:28. Read this one; those two were me moving back and forth while the question was still open, and neither should be worked off.

What settled it is a fact none of the earlier three feedbacks contained, because I had not read runTests.sh:

    # Build/Scripts/runTests.sh:1085-1086
    cglGit)
        ${CONTAINER_BIN} run ${CONTAINER_COMMON_PARAMS} --name cgl-git-${SUFFIX} \
            ${IMAGE_PHP} Build/Scripts/cglFixMyCommit.sh ${CGLCHECK_DRY_RUN}

`-s cglGit` executes that very script inside the typo3/core-testing image. Same file list (git diff-tree over HEAD, filtered to .php), same config (Build/php-cs-fixer/config.php), both fix by default, and `-s cglGit -n` passes the flag straight through, so it equals `./Build/Scripts/cglFixMyCommit.sh -n`. The differences are: the interpreter (image PHP versus the caller's `which php`, which is what PHP_CS_FIXER_IGNORE_ENV existed to paper over), the script's own `-f cache|stdin` options, which runTests.sh cannot pass, the temporary xdebug-free php.ini the script builds for a host it knows nothing about, and the git worktree trap the test-run guide already documents for the containerised form.

Because it is the same script, "run it in the container" costs nothing and removes both the environment prefix and the override. AGENTS.md now names only `./Build/Scripts/runTests.sh -s cglGit`, and the direct invocation appears nowhere.

The one thing worth keeping from the first feedback of the session: where typo3_project_describe reports an environment it found and did not enter, saying which commands run through it is useful. The example just should not be this one.

## Query

Read Build/Scripts/cglFixMyCommit.sh whole and grepped the cglGit case in Build/Scripts/runTests.sh (lines 1085-1086), after the user asked "gibt es unterschiede zwischen ./Build/Scripts/runTests.sh -s cglGit und dem script?"

## Suggestion

Where a workflow tells an author to fix coding guidelines after committing, name `./Build/Scripts/runTests.sh -s cglGit` — it is the same script under a PHP that php-cs-fixer accepts, so no environment prefix and no PHP_CS_FIXER_IGNORE_ENV are ever needed. Mention the direct script only for what the suite cannot reach: `-f cache` for the staging area and `-f stdin` for a file list. Worth carrying alongside the existing worktree warning in typo3_test_run_guide, since that warning is about the containerised form of exactly this script and the two belong in one sentence.
