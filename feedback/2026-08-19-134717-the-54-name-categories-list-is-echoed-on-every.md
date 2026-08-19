---
date: 2026-08-19T13:47:17+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# the 54-name categories list is echoed on every answer, including calls with no category filter

## Observation

Task: find all Forge issues by Frank Nägler — three typo3_forge_lookup calls, none of them passing `category`.

Every one of the three answers carried the full `categories` array, all 54 core category names, plus `categoriesUsed: []`. I passed no category and used none of it. Across three calls that is the same block of roughly 600 tokens repeated three times — more payload than the four open issues I was actually reporting on.

The description explains the intent: the list comes back so "a word matching none is corrected without a second call". That is a good reason to return it when `category` was passed and matched nothing, or matched several. It is not a reason to return it when `category` was never passed at all, and it is not a reason to return it identically again on the second and third call of the same session, where the caller already has it.

## Query

Three typo3_forge_lookup calls in one session, none passing `category`; each answer carried the full 54-entry `categories` array and an empty `categoriesUsed`. Example: {"open":"oldest","reportedBy":"Frank Nägler","status":"all","limit":50}.

## Suggestion

Return `categories` only where it can do the job it exists for: when a `category` argument was passed and resolved to nothing, or resolved to more than one area. When no `category` was given, drop it — or reduce it to `categoriesUsed`, which is the part that describes the answer rather than the vocabulary. A caller who wants the vocabulary without asking a question can get it from typo3_server_scope.

Worth checking whether the other constant vocabularies in this server's answers are echoed the same way.
