---
date: 2026-08-25T11:46:17+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# summary-length-preferred names the budget but returns the over-long subject unchanged

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

An answer that was right and stopped one step short.

typo3_commit_message_guide flagged my 56-character subject and gave the budget precisely: under 52 total, leaving 43 for the summary. What it did not do is put a fitting subject into the returned `message` field. That field came back carrying my over-long subject verbatim, rewrapped only in the body. The one element the tool had objected to was the one element it handed back unchanged.

The contrast inside the same response is what makes this sharp: body-lines-reflowed reported that three paragraphs had been joined and rewrapped at 72 characters, and the returned draft actually contained them rewrapped. The body gets diagnosis plus repair. The subject gets diagnosis only.

The step I took myself: wrote candidate subjects by hand and measured them in bash with ${#s} —
  56  [BUGFIX] Make the git based CGL suites work in worktrees  (rejected)
  46  [BUGFIX] Make CGL suites work in git worktrees            (chosen)
  46  [BUGFIX] Run git-based CGL suites in worktrees
  40  [BUGFIX] Fix CGL suites in git worktrees
then made a second call to confirm the chosen one came back clean. Two round trips and hand-measurement for a transformation the tool was better placed to do: it had already split the subject into keyword prefix and summary and computed both lengths, so it held every input the shortening needs.

Separately, the returned draft is not directly usable for another reason: it strips Signed-off-by (correctly by its own rule, and it says so). Between the unshortened subject and the stripped trailer I assembled the final message by hand rather than using the field at all — which is a shame, because the body reflow in it was good.

## Query

typo3_commit_message_guide(message="[BUGFIX] Make the git based CGL suites work in worktrees ...", workflow="core", releases=["main","14.3","13.4"], issue="110534") — check summary-length-preferred fired, returned message field kept the 56-char subject

## Suggestion

When summary-length-preferred fires, offer at least one subject that fits the budget while keeping the keyword and the meaning — either in the check message or as a dedicated field beside `message`. The tool already has the parse and the remaining character count. Failing that, apply the shortening to the returned `message` the way body reflow is applied, so the draft that comes back passes the tool's own checks; a returned draft that still trips a check the same response just reported leaves the caller unsure whether the field is meant to be used.
