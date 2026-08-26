---
date: 2026-08-26T22:33:25+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3-core-issue-triage, typo3-core-patch-development, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# the stated entry point and both fitting skills stayed shut for a textbook triage-then-patch task

## Observation

Task: "please find 1 old forge issue and fix it" — then narrowed to Extbase. This is triage followed by patch development, which is exactly the split the two published skills describe. I activated neither. I also loaded typo3_task_guide's schema in my very first ToolSearch call and then never invoked it, across ~85 tool calls and nine user turns.

The server's own instructions are explicit: "Then call typo3_task_guide for the workflow the task belongs to, and again at the first test, check, commit or shipped file the task did not name." I passed at least four such moments — first functional test, first cgl run, first phpstan run, the commit message — and called it at none.

Worth reporting honestly rather than as an accident, because a skill is chosen on its description alone:

- typo3-core-issue-triage reads as being for people who want a triage *verdict* as the deliverable — "say what is still true about one issue ... Writing or reviewing a patch is other work". My task explicitly ended in a patch, so the closing sentence read as an exclusion. In fact its body was most of my first hour.
- typo3-core-patch-development reads as being for a patch I already have ("Write a TYPO3 core patch and carry it to review"). At the moment of choosing I had no issue yet, so it read as premature. By the time I had one I was deep in code and never reconsidered.
- typo3_task_guide's description opens "Build a task checklist enriched with matching hints and relevant core checks". At the moment I had "find an old issue and fix it", that read as scaffolding for work I already knew how to sequence — I had AGENTS.md in context with the test-first rule, runTests.sh invocations, CGL and commit-message conventions. The overlap is real, and it is why I never felt a gap.

What it cost, concretely: I derived the whole workflow from AGENTS.md instead. That mostly held — test-first was followed, cgl/phpstan were run, the commit message obeyed the 72-column and footer rules. Two things it did not cover, and I had to guess or flag: the maintained release branches for the Releases: trailer (filed separately) and whether a bugfix of this shape owes a changelog RST (I decided no, unverified).

Also unread: typo3_project_describe returned a `guides` array of 19 documentIds — including core/contribution/changelog, core/contribution/commit-messages, core/contribution/rules, core/testing/scripts. I saw that list in the JSON and read none of them, because they arrived as a field inside a larger answer rather than as something to act on, and typo3_rule_lookup was never in front of me at a moment I felt uncertain. I never checked whether the server exposes MCP resources proper either — ListMcpResourcesTool sat in my deferred-tool list unloaded.

## Query

Task text: "please find 1 old forge issue and fix it", later "please find something in extbase". Skills offered and not invoked: typo3-core-issue-triage, typo3-core-patch-development. Tool schema loaded and never called: typo3_task_guide. Guides listed by typo3_project_describe and never read via typo3_rule_lookup.

## Suggestion

The descriptions are competing with a CLAUDE.md/AGENTS.md that is already in context and already covers the 80% case. That is the thing to design against, not general vagueness.

Concrete moves:

1. Let typo3-core-issue-triage's description claim the *front half of a patch task*, not only standalone triage. Something naming the actual trigger: "before fixing an old issue, whether it still reproduces and whether it is worth the patch". The current closing line "Writing or reviewing a patch is other work" actively pushed me off it.

2. Give typo3_task_guide a description that names what it knows that a repository's own AGENTS.md cannot: the maintained branches, whether this change shape owes a changelog, which check the project declares for this file type. As written it promises a checklist, and a model with AGENTS.md in context already believes it has one.

3. Surface the `guides` list as an instruction rather than a data field — e.g. have typo3_project_describe's guides entries carry a one-line "read this when" so a documentId turns into a decision. Right now 19 ids arrive with titles only, inside a large JSON blob, and nothing marks which one answers a question I am about to get wrong.
