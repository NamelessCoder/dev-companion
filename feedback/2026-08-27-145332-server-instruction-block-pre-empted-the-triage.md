---
date: 2026-08-27T14:53:32+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3-core-patch-development, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# Server instruction block pre-empted the triage and patch skills; neither activated

## Observation

Task: "please search for 1 workspace bug in forge and fix it" — pick a workspaces bug off forge.typo3.org and patch the core checkout. I can see the whole session; there is no summary of earlier turns in my window.

No skill activated. Not one. The task is a near-verbatim restatement of two skill descriptions that were in my listing: typo3-core-issue-triage ("Find the issues worth working on in an area of the forge.typo3.org backlog ... A task that ends in a patch starts here") and typo3-core-patch-development ("Write a TYPO3 core patch and carry it to review"). The request contained the words "search", "forge", "bug", "fix". If those two descriptions do not fire on that sentence, it is hard to construct the sentence they do fire on.

The reason is visible in my own first move. The server's MCP instruction block opens with an unconditional directive: "Start every task with typo3_project_describe ... Then call typo3_task_guide for the workflow the task belongs to". That is a complete, imperative opening procedure, delivered before I had read the skill list carefully, and I followed it literally as my first two calls. By the time typo3_task_guide came back it had handed me a checklist, hints, checks and nextTools — a plan. Nothing after that felt like a gap a skill would fill, so no skill was ever considered again.

So the instruction block and the skill descriptions are competing for the same moment, and the instruction block wins because it is phrased as a command and arrives first. This is not a complaint about typo3_task_guide, which was good (see my separate feedback). It is a report that the two entry points are not composed: the server has published skills for exactly this task shape and then written an opener that routes past them.

The practical loss: typo3-core-issue-triage claims to answer "whether it still happens against the core checkout". I did that step by hand — read WorkspaceService.php, compared three sibling methods, found the missing parameter — and I would do it by hand again next session, because nothing pointed me at the skill.

## Query

Task text: "please search for 1 workspace bug in forge and fix it". Full session visible in my window, no earlier-turn summary. Skills offered to me in the listing included typo3-core-issue-triage and typo3-core-patch-development.

## Suggestion

Decide which layer owns the opening move and make the other defer to it.

If the skills are meant to own task shapes like this, the instruction block should say so: "Where a listed skill covers the task shape — triaging the backlog, writing a core patch — activate it; it will call typo3_project_describe and typo3_task_guide itself." Right now the block reads as if the tools are the whole entry point.

If the tools are meant to own the opening, then typo3_task_guide's answer is the place to hand off: its `skills` field came back empty (`"skills":[]`) for changeType "bugfix" on a core path, while `nextTools` was fully populated. A task_guide answer that named typo3-core-patch-development in `skills` would have activated it at exactly the right moment, after the routing question was already settled. That field existing and being empty for the most obviously skill-covered task in the repertoire is the concrete bug here.
