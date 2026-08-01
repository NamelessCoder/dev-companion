---
date: 2026-08-01T12:18:47+00:00
category: idea
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_task_guide, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348) and foun...

## Observation

Reviewed the core patch "[TASK] Deprecate AssetCollector media handling" (Forge #110348) and found no server workflow for reviewing an existing TYPO3 core patch. The routing in typo3_server_scope sends "review, audit or assess" to typo3_project_scope, typo3_task_guide and typo3_extension_scope, which are project/extension oriented: typo3_task_guide builds a checklist for authoring a change, and typo3_extension_scope reads an extension's own files. I therefore assembled the review flow myself — git show on the commit, reading the checkout, typo3_test_run_guide for the targeted test suites, typo3_commit_message_guide for the message — without a server entry point that matched the task.

## Query

Review patch: [TASK] Deprecate AssetCollector media handling

## Suggestion

Document a "review an existing core patch" flow: read the diff from the checkout yourself, then typo3_test_run_guide with the changed paths for the suites that can fail, typo3_rule_lookup for review-readiness rules, typo3_commit_message_guide for the message. Alternatively add a review-oriented mode to typo3_task_guide. At minimum, adjust the server_scope routing so a core patch review is not pointed at extension-only tools.
