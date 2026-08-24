---
date: 2026-08-24T17:32:36+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-core-issue-triage, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# task_guide schema was fetched and never called; both matching skills stayed shut all session

## Observation

Task: pick another old open issue from forge.typo3.org, branch, and work it off, on a core checkout at 15.0.0-dev.

Two routing mechanisms existed, both matched the task, and neither fired. This is a report about why, from the transcript rather than from impression.

typo3_task_guide: the server's initialize instructions say "Start every task with typo3_project_describe ... Then call typo3_task_guide for the workflow the task belongs to". I loaded its schema in my very first call, called typo3_project_describe, and then never called task_guide. What I was doing at that moment: I had just gotten back a list of 1483 open bugs and was deciding which to read. I judged that a checklist would not tell me which issue still reproduces, which was my actual blocker. Reading its description again now, that judgment was wrong on its own terms — the description explicitly says triage "is answered here" and that it "names what you have to establish there yourself", which is exactly the shape of what I needed. The description is long enough that the triage clause is buried behind a paragraph about checklists and hints, and "checklist" is the word that decided me against it.

The skills typo3-core-issue-triage and typo3-core-patch-development: both were in my available-skills list with descriptions that match this task almost literally — the first says "Say what is still true about an open issue on forge.typo3.org: whether it still happens against the core checkout, was fixed, or was never a defect", which is verbatim the work I did four times over by hand. I never invoked Skill. The moment one would have had to fire was my second turn, holding the oldest-bugs list, about to open #35069 and #59225. The words in front of me were the user's "hole dir ein weiteres altes issue" and a page of tracker rows.

Why it did not fire, honestly: my own operating instructions for this session carried "Do not call the AgentTool unless the user requested it. Do not use workflows or deep-research unless the user requested it", and I generalised that caution to skills, which it does not cover. That is a client-configuration interaction, not a defect in the skill descriptions — but it is the kind of thing the server can only learn if someone reports it.

Also never called all session: typo3_rule_lookup, typo3_hint_lookup, typo3_documentation_lookup, typo3_changelog_lookup, typo3_test_run_guide, typo3_script_lookup, typo3_server_scope, typo3_reference_list. typo3_changelog_lookup is the sharpest miss — asking it what changed in DataHandler localization across 14.x would very likely have settled #86711 in one call instead of the six git calls it took.

## Query

ToolSearch("select:typo3_project_describe,typo3_task_guide,typo3_forge_lookup,typo3_commit_message_guide") as the first call of the session. typo3_project_describe was then called; typo3_task_guide was never called despite its schema being loaded. The Skill tool was never invoked. Task text: "hole dir ein weiteres altes issue, erstelle einen branch und arbeite es ab".

## Suggestion

Move the triage clause of typo3_task_guide's description to the front. An agent scanning it decides on the first clause, and the first clause is currently about building a checklist. Something opening "Says what a task owes before you start it — including triage: whether an open bug report still holds, what a previous attempt cost, what a maintainer would need" would have caught me.

For the skills: typo3-core-issue-triage's description is good and matched. The failure was that nothing at the point of need connected "the user asked me to work off an old issue" to "there is a skill for deciding whether it still reproduces". If typo3_task_guide had been called it would apparently have done that routing — so the two failures are one failure, and fixing task_guide's discoverability fixes both.

Worth testing against a later version: re-run the bare task text "fetch another old issue from Forge, create a branch, work it off" and see whether an agent reaches typo3_task_guide before it reaches typo3_forge_lookup(open="oldest"). In this session it did not.
