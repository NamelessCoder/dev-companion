---
date: 2026-08-28T07:41:42+00:00
category: idea
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3_server_scope, typo3_project_describe
directory: /home/benji/projects/bootstrap_package
---

# the stale-skills notice fires before the task is known and was passed over for a whole session

## Observation

Task: review pull request #1613 against bootstrap_package, then fix it and cover it with a test.

The server's instructions, delivered to my client at initialize time and placed in my system prompt, open with: "The task skills installed in this project are stale; run typo3-dev-companion update." I read it, did not act on it, and then spent the entire session inside those exact skills — typo3-extension-patch-review, typo3-extension-health, typo3-content-element-development, typo3-extension-testing — reading their references/base.md and references/checklist.md as authoritative.

I cannot say whether that cost anything, and that is the finding. I have no way to tell which of the behaviours I attributed to those skills are current and which are from a version the server has since moved past. If, for instance, the crossing gate I filed separately as missing already exists in the current skill text, then my bug report is about a stale copy and wastes a maintainer's time. Neither of us can distinguish those cases from here.

Why it was passed over, concretely. The notice arrives before any task is known, in the same block as the routing advice ("Start every task with typo3_project_describe"), and reads as background rather than as a precondition. Nothing repeated it: typo3_project_describe did not mention it, typo3_task_guide did not mention it while naming two of the stale skills as the ones owning my task, and no skill file said which version of itself it was. The Skill tool call succeeded, the file loaded, and there was no seam at which staleness would have shown.

The update itself is a change to the user's checkout, which I would have had to ask for. That is a real cost and a real reason to defer it — but I did not defer it deliberately, I simply did not carry the notice forward past the first turn.

## Query

Server instructions at initialize time, as delivered in the client system prompt: "The task skills installed in this project are stale; run typo3-dev-companion update."

Skills subsequently activated from .claude/skills/ in the user's checkout: typo3-extension-patch-review, typo3-extension-health, typo3-content-element-development, typo3-extension-testing. `typo3-dev-companion update` was never run.

typo3_task_guide task="Review an incoming pull request that changes an f:if condition in a Fluid partial of the table content element", changeType="audit" — returned skills:["typo3-extension-health","typo3-extension-patch-review"] without reference to their staleness.

## Suggestion

Put the notice where the stale artefact is actually used, not only where the session starts.

typo3_task_guide already returns a `skills` array naming the skills that own the task. Where the installed copy of one of them is behind what the server ships, say so on that entry — a version, a date, or just a flag with the update command. That is the moment a session is about to load the file, and it is the only moment at which the notice competes with nothing else.

Second, and cheaper: have the skill files carry the version they were generated from, so a session reading one can quote it in a feedback. Every finding I filed this session about skill behaviour is unanchored, because I can name the text I read but not which release it belongs to. A single line at the top of each generated skill would make the difference between a report a maintainer can check and one they have to reproduce.
