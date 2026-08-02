---
date: 2026-08-02T23:51:31+00:00
category: bug
status: open
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', sec...

## Observation

Task: REVIEW-03, the forward review 'review the current changes in this TYPO3 core checkout', second run, 2026-08-03 in /home/benji/projects/typo3-cms with the typo3-core-patch-review skill published. typo3_commit_message_guide measures a message it rewrote rather than the one it was given. Called with the patch's own message, subject '[WIP][BUGFIX] Parse User TSConfig for user settings', and changeType BUGFIX, it returns the corrected message with the subject '[BUGFIX] [BUGFIX] Parse User TSConfig for user settings' — it prepends the declared keyword and leaves the unknown [WIP] in place instead of replacing it. It then runs its checks over that rewrite, so two of the three come back wrong: 'The summary line is 55 characters long' for a subject that is 46, and 'Start the summary text with a capital letter after the keyword' for a subject whose text starts with a capital P — the character after the first keyword is the '[' of the second one. The third check, ERROR unknown keyword [WIP], is correct and is the one that matters. The reviewing session caught all of this, reported the ERROR as a blocker and discounted the two warnings as artifacts, which is the right reading and cost it a paragraph of the review to explain. A session that trusted the answer would have filed two false findings against a patch author. Reproduced by hand while judging the run.

## Query

typo3_commit_message_guide with message '[WIP][BUGFIX] Parse User TSConfig for user settings\n\n(wip)\n\nResolves: #110346\nReleases: main, 14.3\nChange-Id: I...' and changeType BUGFIX

## Suggestion

Correct the message before checking it, and check what will be committed rather than an intermediate: replacing an unknown keyword with the declared one rather than prepending it fixes both artifacts at once, because the length and the capital-letter checks then run over a subject that has one keyword. Where the correction cannot be certain — two plausible keywords, or a subject that carries none — saying that the checks below describe the corrected message would at least tell a caller which text the numbers are about.
