---
date: 2026-07-29T17:09:04+00:00
category: idea
status: closed
closed: 2026-07-29
commit: ad00f66
subject: "[TASK] Point the brief at the guide for the step it ends with"
tool: typo3_task_guide, typo3_commit_message_guide
directory: /home/benji/projects/site-new
---

# THE COMMIT MESSAGE GUIDE EXISTS AND I NEVER REACHED IT. Reporting my own miss, because the routin...

## Observation

THE COMMIT MESSAGE GUIDE EXISTS AND I NEVER REACHED IT. Reporting my own miss, because the routing had a hand in it.

I wrote four commit messages in this session without calling typo3_commit_message_guide once. Checking one of them afterwards with workflow="project" produced two findings, and both apply to all four:

- The body is wrapped at 78–79 characters. The rule is 72, and the tool rewraps it correctly on its own.
- The summary lines are 52, 60 and 62 characters. Below 52 is preferred.

So the guide would have improved every one of them, and the fix is free — it hands back a ready-to-commit draft.

Why I did not get there. typo3_server_scope does list it: "When: Writing or amending the commit message". I read that table once, at the start, and by the time I was actually committing — hours and several phases later — I was working from typo3_task_guide, which I called before each phase. Its checklist ends with "Summarize changed behavior, affected area, and executed commands", which is the commit message in all but name, and its nextTools list offers typo3_architecture_lookup, typo3_feedback_record and (in the core profile) typo3_test_run_guide. Never the commit message guide. The one moment where the guide is the obvious next step is the one moment nothing points at it.

The larger share of this is mine — the routing table is there and I did not re-read it. But a checklist item that describes writing the commit message, in a tool built to hand out next steps, is the natural place for the pointer.</observation>
<parameter name="suggestion">Add typo3_commit_message_guide to the nextTools of typo3_task_guide, with when: "before committing", and consider phrasing the checklist item as "Write the commit message with typo3_commit_message_guide" instead of "Summarize changed behavior". A workflow="project" default would follow from outsideCore, which task_guide already computes.

## Query

typo3_task_guide (four calls across this session, changeType feature/test, targetVersion 14.3) — its checklist ends with "Summarize changed behavior, affected area, and executed commands" and its nextTools list never names typo3_commit_message_guide
