---
date: 2026-08-08T22:44:13+00:00
category: missing-knowledge
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# typo3-core-patch-checkout stayed shut on a request whose literal word was worktree, because its d...

## Observation

Task: review Gerrit change 95179 in a git worktree of a TYPO3 core checkout.

Skill activated: typo3-core-patch-review. It fitted the task well and I would keep it as it stands. Its ordering (establish the patch, then the rules it is judged by, then read the diff a second time) is what produced the two findings that mattered, and its scratch-probe paragraph is what let me turn "this would presumably change behaviour" into a pasted before/after test result. Nothing in it I would drop.

The skill that existed and stayed shut is typo3-core-patch-checkout. The moment one would have had to activate is exact: I had just received the gerrit_lookup answer (change 95179, patch set 1, commit 0b18ff0af75), the user's sentence "kannst du das bitte im worktree reviewn?" was the whole brief, and I had no files open yet. The review skill's own routing line even names it: "Getting a patch into a checkout belongs to typo3-core-patch-checkout". I read that line and went to raw git fetch plus git worktree add --detach anyway.

The reason is in the description, and it is a wording problem rather than a coverage one. It reads: "Get a patch under review on review.typo3.org into a core checkout and back out again: find the change, fetch the patch set, put it on the branch it targets, rebase it, and restore a clean current branch." Every verb after "fetch" describes mutating the branch I am standing on and then undoing it. A worktree review is the opposite operation: leave the current branch untouched, materialise the patch beside it. So the description did not read as ambiguous, it read as actively not my case. The word "worktree" does not occur in it. It does occur, twice and with real content, in what typo3_test_run_guide returns.

Skills are chosen on their description alone, and this one describes a branch-switching workflow while a neighbouring tool knows that worktrees are a first-class way to do the same job.

## Query

User request, verbatim: "kannst du das bitte im worktree reviewn? https://review.typo3.org/c/Packages/TYPO3.CMS/+/95179"

## Suggestion

Name the worktree case in typo3-core-patch-checkout's description, so the word a user actually types reaches it, for example: "... into a core checkout or a separate git worktree beside it ...". If the skill's body does not currently cover the worktree path, it is worth adding, because that path has a precondition the branch path does not: a fresh worktree has no vendor/ or bin/ (both gitignored) and needs CI=true ./Build/Scripts/runTests.sh -s composerInstall before any suite will run. typo3_test_run_guide already carries that fact; the checkout skill is where somebody creating the worktree meets it first.
