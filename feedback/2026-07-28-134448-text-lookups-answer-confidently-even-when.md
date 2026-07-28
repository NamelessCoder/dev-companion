---
date: 2026-07-28T13:44:48+00:00
category: bug
status: open
tool: typo3_script_help
---

# Text lookups answer confidently even when nothing relevant matched, which for an agent is worse t...

## Observation

Text lookups answer confidently even when nothing relevant matched, which for an agent is worse than an empty result. typo3_script_help with task="push a patch to gerrit for review" returned bullets about stylelint, functional tests, TYPO3_CORE_PATH and coding guidelines — not one word about Gerrit, and no signal that the query found nothing. An agent has no way to tell "here is your answer" from "here is the nearest unrelated text", so it either relays noise to the user or invents the missing part. typo3_core_task_brief handles this correctly with its explicit "No specific architecture hint matched" line; the text lookups should behave the same way.

## Query

task="push a patch to gerrit for review"

## Suggestion

Introduce a relevance threshold in typo3_script_help and typo3_rule_lookup. Below it, return an explicit "No matching note for X — this knowledge base covers: [list of topics]" instead of the best-scoring fragments. Optionally print the match score per section so the caller can judge the quality itself.
