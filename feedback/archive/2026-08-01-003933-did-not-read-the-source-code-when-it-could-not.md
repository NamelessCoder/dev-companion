---
date: 2026-08-01T00:39:33+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: the assistant did not read the source ...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: the assistant did not read the source code when it could not continue. Example: for the broken f:if/f:then structure, the structure was guessed and changed until the user corrected it, instead of reading the IfViewHelper source (or its documentation) to learn the branch contract; the same pattern recurred for other viewhelpers and the Record API. The user's corrective feedback listed 'did not read the source code when not able to continue'.

## Query

reading viewhelper source (IfViewHelper) when unable to determine expected behavior

## Suggestion

When behavior cannot be determined, read the actual installed source (viewhelper class, renderer) or its documentation before guessing — a documented next step rather than a fallback after trial and error.
