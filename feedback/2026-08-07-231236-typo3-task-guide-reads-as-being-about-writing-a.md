---
date: 2026-08-07T23:12:36+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_task_guide reads as being about writing a patch, so a decide-whether-to-patch task never re...

## Observation

Task: decide whether a 2006 bug report still reproduces, and report the cost of fixing it, explicitly before touching anything.

The server instructions say to open with typo3_project_describe and that "typo3_task_guide then gives the workflow the task belongs to". I made the first call and skipped the second. The reason is the sentence immediately after it: "The coding agent writes the patch; this server supplies the task knowledge and workflows around it." My task ended one step before a patch — the user's closing words were "before I touch it" — so the guide read as addressed to work I was deliberately not doing. I judged that a read-only triage had no workflow to look up, and went straight to the tracker.

I would make the same call again from the same wording, and that is the finding. Backlog triage, "is this still reproducible", and "what would this cost to fix" are recurring tasks that end before a patch exists, and the framing of the entry point does not claim them.

No skill activated in this session. Within my visible transcript there is no Skill call at all, and the skills my client listed were generic ones — dataviz, artifact-design, artifact-diagramming, artifact-capabilities, update-config, keybindings-help, simplify, fewer-permission-prompts, loop, schedule, claude-api, run, init, review, security-review — with no TYPO3 skill among them. So the instruction about handing parts to "the skill that owns them" had nothing to hand to in this environment, which is worth knowing separately from whether any skill would have fitted. My window begins at the user request and the system prompt carries an "Exited Plan Mode" marker, so I cannot speak for a planning exchange that may precede what I can see.

## Query

Whole session. typo3_project_describe was called as instructed; typo3_task_guide was never called and its schema was never loaded. Task text: "take the first one that looks like a real bug and tell me whether it is still a thing. I want to know what I would be signing up for before I touch it."

## Suggestion

Say in the server instructions, and in typo3_task_guide's own description, that it covers tasks ending before a patch — triage, reproduction, assessing what a fix would cost — and not only tasks producing one. If it does not cover them today, that is the gap itself: "establish whether an old report still reproduces on the current branch, and what the last attempt cost" was the workflow this session needed and built by hand.
