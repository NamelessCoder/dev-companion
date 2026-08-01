---
date: 2026-07-28T15:06:35+00:00
category: bug
status: closed
closed: 2026-07-28
commit: f7c6016
subject: "Hold prose tool names to the registry"
tool: typo3_core_task_brief
---

# The checkoutDiscovery prose names three tools that do not exist on this server: "call typo3_archi...

## Observation

The checkoutDiscovery prose names three tools that do not exist on this server: "call typo3_architecture_lookup with those paths", "ask typo3_test_run_guide for the targeted runTests.sh invocation", and "typo3_catalog_scope names it". The real names are typo3_architecture_hint, typo3_core_run_tests_help and typo3_catalog_status. The nextTools array in the very same response payload uses the correct names, so the two are out of sync. This is worse than a cosmetic typo: an agent reading the prose issues a tool call that fails, and the prose is exactly the part meant to steer it after the brief. Reproduced in three consecutive calls with different tasks and changeTypes. Worth flagging: the first typo3_core_task_brief call I made in this session (task about FormEngine TSconfig label overrides, area backend/FormEngine, changeType bugfix) returned the same checkoutDiscovery block with the correct names, and typo3_server_scope also returns the correct names in its own checkoutDiscovery. So either there are two variants of this text in the knowledge base and only one was renamed, or something changed mid-session. Both are worth checking, since the identical text lives in at least two places.

## Query

task="Add a new page TSconfig option to configure the file list module and document it"; task="Add a user TSconfig option for the clipboard"; task="Fix a DataHandler bug when copying records"

## Suggestion

Fix the tool names in the checkoutDiscovery text and de-duplicate it: typo3_server_scope and typo3_core_task_brief should render the same block from one source. Better, stop hardcoding tool names in prose and reference them through the same registry that produces nextTools, so a rename cannot leave stale names behind. A cheap regression guard is a test asserting that every tool name appearing in any knowledge document or generated prose is a registered tool of this server.
