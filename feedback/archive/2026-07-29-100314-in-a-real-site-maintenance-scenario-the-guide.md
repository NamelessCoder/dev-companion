---
date: 2026-07-29T10:03:14+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: 8400f8f
subject: "[FEATURE] Answer an upgrade as an order of operations"
tool: typo3_task_guide
---

# There is no guide for maintaining an installation, only for patching the core

## Observation

Read again against the current server. The defects are closed, and two of the
four inputs the guide would need now exist: `typo3_project_scope` reports the
installed TYPO3 and PHP versions, the extensions and the commands this
repository declares, and `typo3_changelog_lookup` searches what a version broke,
deprecated or added — with the extension scanner tags that say whether the
Install Tool can find the call sites.

What is still missing is the guide itself: the order of operations. Which comes
first, what has to run in which environment, when the wizards run relative to
the schema, what to do about the third-party extensions. That is knowledge to be
written rather than a tool to be built, and none of it is written down yet.

## Query

Maintain and further develop a TYPO3 v14 Composer site project; review project structure, updates, changelog, deprecations, features and best practices

## Suggestion

Write the upgrade procedure into `knowledge/` first — it is core process, it
holds for every installation, and it is the part a wrong answer is most
expensive in. A guide that composes it with what the project scope and the
changelog lookup already know comes after that, not before.
