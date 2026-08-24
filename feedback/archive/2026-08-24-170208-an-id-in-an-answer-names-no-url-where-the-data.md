---
date: 2026-08-24T17:02:08+00:00
category: wrong-answer
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms-mcp
---

# An id in an answer names no URL where the data half already carries one

## Observation

The task was working this repository's todo queue, reading Forge and Gerrit answers to follow what a change resolves. An agent that repeats a Forge or Gerrit id to a person renders it as a clickable link, and those links partly go out wrong, so the target is not reachable and nobody can check what is being talked about. Measured against the code on 2026-08-24. The text half prints a bare number in three places where the data half already holds the URL: ForgeLookup::relationLine writes 'Relation: relates #110331' with no URL though Forge::HOST . '/issues/' . number is built into the relation record; GerritLookup::issues writes '- Resolves #110493' with no URL though the same URL is built into the issue record; GerritLookup::chain writes the change number alone and the chain record carries no url field at all, so a caller reading a chain has nothing to link from. Where a URL is printed the form is not one form: the review handles Forge derives from an issue's journal are built as review.typo3.org/c/<number>, while a change Gerrit answers for itself is built as review.typo3.org/c/Packages/TYPO3.CMS/+/<number>. Both reach the page — the first answers 302 to the second, checked against review.typo3.org on 2026-08-24 — but an agent that has seen both forms has no single form to copy.

## Query

typo3_gerrit_lookup change="95375"; typo3_forge_lookup issue=110493 — read for the ids each answer names rather than for the change itself

## Suggestion

Every id an answer names carries its URL, in the text half as well as the data half, and in one form. Where the number is all that is carried today — the relation chain — the record gains the URL rather than the caller composing one.
