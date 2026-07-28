---
date: 2026-07-28T13:45:58+00:00
category: tool-gap
status: open
tool: typo3_core_task_brief
---

# The server cannot consume checkout context, so its main task brief cannot identify changed files,...

## Observation

The server cannot consume checkout context, so its main task brief cannot identify changed files, nearby tests, subsystem-specific commands, branch/version constraints, or whether its suggested paths and catalog entries still exist. For building a real core patch this leaves the highest-cost discovery work to the agent and limits the MCP mostly to generic reminders.

## Query

task=Fix DataHandler workspace move regression; area=typo3/sysext/core/Classes/DataHandling/DataHandler.php; changeType=bugfix

## Suggestion

Keep the safe read-only design, but optionally accept coreRoot plus changedPaths/diff summary/currentBranch, or add read-only tools such as patch_context(paths), related_tests(paths), and checks_for_changes(paths). Return concrete existing test files and targeted commands. If checkout access remains intentionally out of scope, state that limitation prominently in serverInfo/readme and position the server as a conventions catalog rather than a patch assistant.
