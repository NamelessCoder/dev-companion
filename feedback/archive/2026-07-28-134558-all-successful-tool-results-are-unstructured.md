---
date: 2026-07-28T13:45:58+00:00
category: idea
status: closed
closed: 2026-07-28
commit: a34cd57
subject: "Answer every tool call as data as well as text"
tool: alllookupandbrieftools
---

# All successful tool results are unstructured Markdown text only. Agents must parse headings, code...

## Observation

All successful tool results are unstructured Markdown text only. Agents must parse headings, code fences and prose to recover commands, paths, identifiers, severity and confidence; this is fragile and makes composing several tools unnecessarily expensive. Tool definitions also omit MCP annotations such as readOnlyHint/idempotentHint.

## Suggestion

Return structuredContent with stable JSON schemas alongside the human-readable text: matches with score/confidence/source/version, checks as command arrays, labels/icons/components as typed records, and commit diagnostics as level/code/message. Add readOnly/destructive/idempotent annotations so clients can safely select and batch tools.
