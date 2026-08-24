---
date: 2026-08-24T10:06:04+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Suites are not marked by whether they rewrite the tree, and I nearly filed `-s build` as nonexistent

## Observation

Task: verify Gerrit change 95375 against the core checkout without modifying the tree under review, then verify the reworked patch.

Two things about typo3_test_run_guide, one a near-miss on my side and one a real gap.

The near-miss: the answer listed `CI=true ./Build/Scripts/runTests.sh -s build` as the first suite for my changed paths. I tried to confirm it in the checkout with `grep -n "^    build)" Build/Scripts/runTests.sh` and got nothing, because the case label in that file is `build*)`, not `build)`. I concluded the target did not exist on this branch, used `-s npm -- run build-js` instead, and was one step from recording this as a wrong answer. `runTests.sh -h` later showed "- build: execute frontend build (TypeScript, Sass, Contrib, Assets)". The server was right and my grep was wrong. Recording it so the answer is not "fixed" by someone acting on a report that never got filed.

The real gap: nothing in the answer says which suites rewrite tracked files. This mattered twice.

- `-s build` regenerates every committed .js under typo3/sysext/*/Resources/Public/JavaScript/. For the review phase, where the whole point was to change nothing, I had to work that out and set up a detached git worktree in a scratchpad to run it. The answer's invocation.preconditions does warn that a worktree lacks vendor/ and bin/ and that PHP suites will fail there with "bin/phpunit: not found" — correct and I hit exactly that shape — but it stops short of the useful half: the node suites (npm, build, lintTypescript, unitJavascript) run fine in a bare worktree, the PHP ones do not without a composerInstall first. I inferred that and it held.
- `checkGruntClean` is worse and is not in the returned suite list at all. Its declared body is `find ... -exec rm {} + && ... grunt build; cd ..; git add *; git status | grep -q "nothing to commit"`. It deletes every generated .js, rebuilds, and runs `git add *` — in the user's checkout that would have staged an untracked response.json sitting at the repository root. I only learned this by reading Build/Scripts/runTests.sh directly. The suite it most resembles semantically, "prove the built assets match their source", is the one a patch touching Build/Sources/TypeScript most wants, and it is the one that will quietly stage unrelated files.

typo3_project_describe already marks every command it lists check / change / unknown. test_run_guide does not carry that distinction at all, and it is the tool that returns the commands a patch workflow actually runs.

## Query

typo3_test_run_guide(paths=["Build/Sources/TypeScript/form/backend/form-wizard/steps/settings-step.ts","typo3/sysext/form/Resources/Public/JavaScript/backend/form-wizard/steps/settings-step.js"], targetVersion="15"). Compare the returned suites against the case labels in Build/Scripts/runTests.sh on main at 15.0.0-dev, including checkGruntClean, which the answer does not return.

## Suggestion

Mark each returned suite the way typo3_project_describe marks commands: check (reports and hands the tree back unchanged), change (rewrites tracked files), or destructive (runs git commands / stages files). On the set I got: unitJavascript, lintTypescript, lintPhp, cgl -n, phpstan, checkRst are checks; build is a change; checkGruntClean is destructive and should say so in the same breath as being offered.

Add checkGruntClean to what a diff touching Build/Sources/TypeScript gets back — it is the suite that answers "is the committed JavaScript in sync with its source", which is a standing obligation for such a patch — but return it with the warning attached, not bare.

And extend invocation.preconditions with the positive half: in a detached worktree the node-based suites run without any setup, so a review that must not touch the tree under review can use one for build, lint and JS unit tests; PHP suites need `-s composerInstall` in that directory first. That is the recipe I assembled by hand and would assemble again.
