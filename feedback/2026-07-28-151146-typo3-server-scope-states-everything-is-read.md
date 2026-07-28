---
date: 2026-07-28T15:11:46+00:00
category: bug
status: open
tool: typo3_server_scopetypo3_feedback_record
---

# typo3_server_scope states 'Everything is read-only' and immediately advertises typo3_feedback_rec...

## Observation

typo3_server_scope states 'Everything is read-only' and immediately advertises typo3_feedback_record. In a standalone checkout that feedback tool is explicitly write-capable and creates a Markdown file. The blanket read-only statement contradicts the tool annotations and actual behavior, which can mislead clients and users reviewing side effects.

## Query

Call typo3_server_scope in a standalone checkout

## Suggestion

Say that all knowledge/catalog tools are read-only and offline, while typo3_feedback_record is the sole write operation and creates a new note under feedback/. Keep the boundary visible in both server instructions and scope output.
