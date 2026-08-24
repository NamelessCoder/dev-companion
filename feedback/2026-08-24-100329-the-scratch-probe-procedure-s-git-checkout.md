---
date: 2026-08-24T10:03:29+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# The scratch-probe procedure's `git checkout --` destroys the change under development

## Observation

Task: review Gerrit change 95375 against a core checkout, then rework it after the user asked for the changes.

references/checklist.md and the skill body prescribe scratch probes like this: "Add a temporary fixture column, a model property or a test of your own, run a targeted suite against it, read what it prints, and put the tree back — `git checkout --` on what you touched, then `git status` to confirm it is clean."

I followed that literally and lost work. To prove a new functional test actually fails when the fix is absent, I edited a constant in typo3/sysext/form/Classes/Controller/FormManagerController.php, ran the targeted suite, saw the expected failure, then ran `git checkout -- typo3/sysext/form/Classes/Controller/FormManagerController.php`. That restored the file to HEAD, which was patch set 1 — so it discarded the probe AND the entire uncommitted change I had just written into that same file. `git status` afterwards reported a clean file, exactly as the procedure says it should, which is precisely why nothing flagged it. I only noticed because a follow-up grep for my own constant returned 0, and I had to reconstruct the edit from scratch.

The procedure is safe only in the case it was written for: probing a file you are NOT also editing. In this workflow the two collide by default, because the file you most want to probe is the file carrying the fix. The verification step and the development step operate on the same path.

Later in the same session I did it the safe way without being told to: `cp <file> <scratchpad>/<file>.keep`, edit, run, `cp` back, then `git diff --stat <file>` to confirm the change was still there. That worked.

This is not a knowledge gap. The instruction as written is actively destructive in the workflow that carries it, and it is destructive silently.

## Query

Skill typo3-core-patch-review, section "Verification is the project's own, and it is narrowed by the diff", the paragraph beginning "A scratch probe is one of the things it may run." Reproduce by: making an uncommitted edit to a file, then making a second temporary edit to the same file, then following the prescribed `git checkout --` restore.

## Suggestion

Split the restore instruction by whether the probed file already carries uncommitted work.

For a file you are not otherwise editing, `git checkout --` is fine and should stay.

For a file that carries the change under development — the common case in this skill, since the fix and the probe target the same code — the restore has to preserve the working-tree state, not the committed one. Copy the file aside before probing and copy it back, or use `git stash push <path>` / `git stash pop`, and verify with `git diff --stat <path>` that the change is still present rather than with `git status` that the file is clean. A clean `git status` is the failure signal here, not the success signal, and the current text tells the reader to look for exactly the wrong thing.

Worth stating the hazard in one sentence too: `git checkout -- <path>` restores to HEAD, and in this workflow HEAD is the patch set you are reviewing, not the work you have done on top of it.
