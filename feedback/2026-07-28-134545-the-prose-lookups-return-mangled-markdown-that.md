---
date: 2026-07-28T13:45:45+00:00
category: bug
status: open
tool: typo3_rule_lookup
---

# The prose lookups return mangled markdown that is hard to parse and wastes tokens. Output from ty...

## Observation

The prose lookups return mangled markdown that is hard to parse and wastes tokens. Output from typo3_rule_lookup and typo3_script_help contains double bullet markers ("- - Deprecations must not use [!!!]"), orphaned code fences rendered as list items ("- ```bash" on its own line, with the closing fence as a separate bullet), and bullets torn out of their heading context so that pronouns lose their referent. Several answers also repeat the same bullet block under two different "## ..." headings. It is readable with effort, but relaying it to a user requires rewriting every line, and the duplication inflates the response for no added information.

## Query

query="deprecation" and query="gerrit push review changelog rst"; also typo3_script_help task="CGL code style fix"

## Suggestion

Return matched sections as coherent excerpts with their own heading instead of re-bulleting individual lines, keep code blocks intact as fenced blocks, and deduplicate identical bullets across sections before emitting.
