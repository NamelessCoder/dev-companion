---
date: 2026-08-24T20:52:23+00:00
category: missing-knowledge
status: closed
closed: 2026-08-26
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

## Query

typo3_test_run_guide(paths=["Build/Sources/TypeScript/form/backend/form-editor/view-model.ts","typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js"], targetVersion="15.0") and typo3_task_guide(changeType="audit", same paths). Both returned checkGruntClean with the git-add warning.

## Suggestion

Where a suite is marked runs "git" and warned against, name what to do instead in the same entry. For checkGruntClean that is: compare the committed artifact against the source change directly, which for a minified single-line file means diffing the two revisions of that one line and printing only the differing region. One sentence and it removes the dead end.

## Trimmed on 2026-08-26

The second half — a suite run from a detached checkout of an older branch dying in Composer's platform check, because the branch's own runTests.sh selects its era's default PHP while vendor/ was installed for main — is answered. `invocation.preconditions` in `knowledge/test-suite-hints.json` now carries the mechanism, the failure text, `-p` as the way past it, and rebasing as the fix. `D-ANS-113` has the reading it was written from.
