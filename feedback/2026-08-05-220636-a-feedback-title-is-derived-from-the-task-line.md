---
date: 2026-08-05T22:06:36+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_feedback_record, typo3_feedback_list
directory: /home/benji/projects/typo3-cms-mcp
---

# A feedback title is derived from the line that names the task

## Observation

Task was to file the debrief of a session that worked off the open todos in this checkout, which came to three feedback recorded in one batch.

The title and the file name are derived from the opening characters of observation, and the tool description asks that observation open with one line naming the task. Those two work against each other: every feedback from one session opens with the same words, so the derived titles collide. What I got was

- "Task was the todo "hold the release lines a commit message claims": establish which TYPO3 branche..."
- "Task was to work off the open todos in this server's own checkout — three cards in todo/open/, tw..."
- "Task was the todo "hold the release lines a commit message claims", which asked for the maintaine..."

The first and the third are the same string up to the truncation and name different subjects — one is a git measurement trap, the other is three properties of the get.typo3.org API. The third one's file name went further and dropped the colliding prefix entirely: it is feedback/2026-08-05-220550-which-asked-for-the-maintained-typo3-branches.md, a slug that opens on a subordinate clause and says nothing about what the feedback reports.

That is R-FBK-008 not holding — a feedback name is supposed to say what only that feedback says — and it bites hardest on the listing, which is where a maintainer decides what to open. typo3_feedback_list and bin/cli feedback:list show the title, so three of them starting on the same clause is three rows nobody can triage without opening all three.

## Query

Three typo3_feedback_record calls in one batch, each with observation opening "Task was ..." as the tool description asks. Categories tool-gap, missing-knowledge, missing-knowledge. All three landed on the same timestamp 2026-08-05-220550 and are distinguished only by the derived slug.

## Suggestion

Either take the title from somewhere that is about the subject rather than about the task — a short subject line the tool asks for as its own parameter is the honest fix, since nothing can derive a subject from prose reliably — or stop asking observation to open with the task, and carry the task in query where it already belongs. As it stands the two instructions cannot both be followed. The batch case is the one that shows it, but a single feedback filed by a session whose task has a long name has the same title.
