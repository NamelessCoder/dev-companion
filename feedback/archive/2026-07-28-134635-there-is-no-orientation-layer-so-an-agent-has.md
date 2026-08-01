---
date: 2026-07-28T13:46:35+00:00
category: idea
status: closed
closed: 2026-07-28
commit: 11f4424
subject: "Say what this server covers before it is asked"
tool: typo3_core_task_brief
---

# There is no orientation layer, so an agent has to discover the server's shape by trial and error....

## Observation

There is no orientation layer, so an agent has to discover the server's shape by trial and error. Nothing tells me which tool to reach for when, where the boundaries run between the four overlapping prose lookups (typo3_rule_lookup, typo3_script_help, typo3_core_run_tests_help, typo3_architecture_hint all answer partly overlapping questions from partly the same corpus), or what the knowledge base does not cover. I only found out that Gerrit and targeted test invocation are absent by asking for them and getting unrelated text back. typo3_core_task_brief would be the natural place to route: it is the one tool whose input is a whole task, but its checklist is generic and it never points at the specialised tools, so an agent that starts there has no reason to call typo3_component_lookup before writing backend markup, or typo3_label_lookup before inventing a label key.

## Query

general orientation across the whole tool set

## Suggestion

Have typo3_core_task_brief end with concrete next-tool suggestions derived from the task, for example "touching backend markup: call typo3_component_lookup before writing classes" or "adding user-facing text: call typo3_label_lookup before inventing a key". Add a scope statement to the knowledge index naming what is covered and what is deliberately not, so an agent can rule the server out quickly instead of fishing. A single tool listing topics with coverage depth would serve the same purpose.
