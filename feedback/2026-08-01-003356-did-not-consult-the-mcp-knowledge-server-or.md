---
date: 2026-08-01T00:33:56+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_task_guide, typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the assistant did not consult the MCP knowledge ser...

## Observation

Debrief of the TYPO3 14 testimonials session: the assistant did not consult the MCP knowledge server or fetch documentation unless the user explicitly asked for it, and this passivity is what made the session fail. The documentation_lookup and changelog_lookup calls were only made after the user pushed: 'please check the documentation', 'do you not think there is any documentation how the data is processed?', 'please check the record api'. Before those prompts the assistant guessed, edited templates, wrote an unverified preview, and read vendor source directly — the reverse of the documented workflow. typo3_task_guide, which exists precisely to start a task with the right workflow and lookups, was never called at all, and no skill was activated. The user's corrective feedback listed 'did not check the documentation', 'did not consult the mcp', and 'did a lot of trial and error instead of consulting documentation'.

## Query

assistant only consulted MCP/documentation after user asked; task guide and skills never invoked proactively

## Suggestion

Make the starting contract explicit in the guidance: every task must begin with typo3_task_guide, the fitting skill must be activated, and documentation_lookup/changelog_lookup must precede any template, TCA, TypoScript, or test edit — never after the user demands it.
