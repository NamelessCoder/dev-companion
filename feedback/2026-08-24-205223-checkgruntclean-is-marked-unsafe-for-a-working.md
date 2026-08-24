---
date: 2026-08-24T20:52:23+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# checkGruntClean is marked unsafe for a working checkout but no alternative is named

## Observation

Task: reviewing Gerrit change 95392, one character in a .ts file plus its committed .js, in the user's own working checkout.

typo3_task_guide and typo3_test_run_guide both returned checkGruntClean with an unusually good warning: runs "git", body deletes every generated .js and runs `git add *` over the whole working tree, "Run it in a checkout whose index you can throw away, and not in one holding work of your own", and the note that a git worktree is not the way out because its gitdir sits outside the mount. I acted on that warning and did not run it — the checkout was the user's, with 23 branches of their own work in it. That warning did its job and should not be touched.

But checkGruntClean is the only suite that answers the question the diff actually raised: does the committed JavaScript match its TypeScript source. The guide named the tool, said do not use it here, and stopped. What I did instead, and would do again with no help from the server:

  git show gr/95392 --format="" -- <the .js path> | grep '^[-+]'
  then a python difflib.SequenceMatcher over the single old and new minified lines, printing the differing region with 90 characters of context either side

That printed `l=i.querySelector(...)` against `l=i?.querySelector(...)` and settled it. It is a general technique for verifying a minified build artifact against its source change without running a build, and it took me three attempts to get readable output out of a one-line 130KB diff.

The same gap appeared once more: for change 94712 I wanted to know whether its stale base broke checkRst, and the answer turned out to be that an old branch's runTests.sh defaults to PHP 8.2 while the installed vendor/ requires ^8.5, so every suite on that base dies in Composer's platform check. typo3_project_describe does state the PHP relation between project, core and environment, and typo3_test_run_guide's preconditions do cover vendor/ missing in a fresh clone or worktree — but neither covers this case, which is vendor/ present and correct for main while the checked-out branch's script picks a different PHP. I diagnosed it by checking out the merge-base and re-running, one container run.

## Query

typo3_test_run_guide(paths=["Build/Sources/TypeScript/form/backend/form-editor/view-model.ts","typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"], targetVersion="15.0") and typo3_task_guide(changeType="audit", same paths). Both returned checkGruntClean with the git-add warning. Second case: ./Build/Scripts/runTests.sh -s checkRst run from a detached checkout of Gerrit 94712 (base fa8e1dfc31b, 187 commits behind main), which failed with "Your Composer dependencies require a PHP version >= 8.5.0. You are running 8.2.31."

## Suggestion

Where a suite is marked runs "git" and warned against, name what to do instead in the same entry. For checkGruntClean that is: compare the committed artifact against the source change directly, which for a minified single-line file means diffing the two revisions of that one line and printing only the differing region. One sentence and it removes the dead end.

Separately, add to typo3_test_run_guide's preconditions the case of running a suite from a detached checkout of an older branch in a repository whose vendor/ was installed for main: the branch's own runTests.sh selects its era's default PHP, the platform check aborts before the suite starts, and the failure names the PHP version rather than the branch. The existing preconditions cover the empty-vendor case and read as if that were the only way the mount can disagree with the script. Worth pairing with the advice to rebase before running anything, which is what actually fixes it.
