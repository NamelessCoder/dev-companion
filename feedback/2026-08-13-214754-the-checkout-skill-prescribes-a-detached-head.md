---
date: 2026-08-13T21:47:54+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout
directory: /home/benji/projects/typo3-cms
---

# the checkout skill prescribes a detached HEAD for a fetched patch set and has no answer for a che...

## Observation

Task, in the user's words: "bitte review mir <change url>, denk daran ihn auf main zu cherry-picken" — review change 93319 and remember to cherry-pick it onto main.

typo3-core-patch-checkout covered the fetch well. What it has no shape for is the request that actually arrived. It offers exactly two ways in — "the branch it targets in the checkout you are standing in, or into a git worktree beside it" — and for the first it prescribes a detached HEAD, with a reason: "A fetched patch set is somebody else's commit and belongs on no local branch of yours until you have decided to carry it on. Detaching onto FETCH_HEAD says that in the checkout, which is what keeps a local branch from quietly acquiring a commit that belongs to a review."

A cherry-pick onto current main is neither of the two. The patch's base was four commits behind main, so I detached onto main and ran `git cherry-pick d86b9fc8b91`. That produces a new commit — 502f8eeb327, not the reviewed 502f8... sorry, not the reviewed d86b9fc — sitting on a detached HEAD with no name at all. The skill's own reason for detaching does not hold for it: it is no longer somebody else's commit, it is a new one this session made, and the argument against naming it evaporates the moment it stops being the fetched object.

The user corrected me mid-session: "wenn du einen checkout und cherry-pick auf main machst solltest du immer einen review branch anlegen". I created review/93319 and the rest of the review ran from there. That is plainly better and I would not have got there from the skill — the skill steers away from it in as many words.

Two knock-on effects the skill also does not follow through on. Its "Put the checkout back" section is written for a detached HEAD and a rebase; step 2 says to return to the recorded branch and that "the patch set was fetched onto no branch, so leaving it loses nothing that is not still on the review server". With a review branch that is no longer true — the cherry-picked commit exists only locally, and the undo is a deliberate `git branch -D` rather than letting a detached commit fall away. And the review skill's checklist ends on "The working tree around the patch", which a named branch makes answerable and a detached HEAD makes easy to lose track of.

Separately: the skill's routing at the end worked exactly as written. It handed the judging to typo3-core-patch-review and said the review starts from the working copy this leaves behind, before the undo runs. I followed that and it was the right order.

## Query

typo3-core-patch-checkout, invoked with "https://review.typo3.org/c/Packages/TYPO3.CMS/+/93319 auf main cherry-picken"

## Suggestion

Add the cherry-pick as a third way in, beside the detached checkout and the worktree, because it is a thing people ask for by name — "cherry-pick it onto main" — and it answers a question the other two do not: does this patch still apply to current main, and does it still pass there.

Prescribe a named review branch for it, `review/<change number>` or similar, and say why the detach argument does not carry over: a cherry-picked commit is not the fetched object any more, and the thing the detach was protecting against has already happened. Say plainly which commit the findings are then about — the cherry-pick's hash is not the patch set's, and a review that quotes the local hash without saying so is unattributable.

Extend "Put the checkout back" for that path: the branch has to be deleted explicitly, and the answer says the branch is gone rather than that the checkout is detached-free. Right now that section reads as complete and silently covers only two of the three ways in.
