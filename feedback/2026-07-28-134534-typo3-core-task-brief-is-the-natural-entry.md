---
date: 2026-07-28T13:45:34+00:00
category: wrong-answer
status: open
tool: typo3_core_task_brief
---

# typo3_core_task_brief is the natural entry point for an agent, but it does not consult the rules ...

## Observation

typo3_core_task_brief is the natural entry point for an agent, but it does not consult the rules corpus the same server already ships. For task="Deprecate a public method and add a changelog entry" (area=core, changeType=cleanup) it answered "No specific architecture hint matched" and "No topic-specific check matched", and its checklist said nothing about a changelog. Yet typo3_rule_lookup with query="deprecation" returns exactly the rules this task needs: deprecations must not use [!!!], may only use [TASK] or [FEATURE], must be documented with a changelog RST file below typo3/sysext/core/Documentation/Changelog/, need migration guidance and extension-scanner consideration, and require "./Build/Scripts/runTests.sh -s checkRst". The knowledge exists but is not wired into the one tool an agent calls first — and the task text literally contained the word "changelog".

## Query

task="Deprecate a public method and add a changelog entry", area="core", changeType="cleanup"

## Suggestion

Let typo3_core_task_brief fold in matching rule and commit-message knowledge, not just architecture hints and suite hints. For a deprecation it should produce the changelog RST requirement, the [!!!] prohibition, the keyword restriction and checkRst as checklist items. Deriving intent from changeType would help too: changeType=cleanup plus "deprecate" in the task text is a deprecation, which is one of the most rule-heavy task types in the core.
