---
date: 2026-07-29T10:03:14+00:00
category: tool-gap
status: open
tool: typo3_task_guide
---

# In a real site-maintenance scenario the guide correctly sets outsideCore=true but then returns mo...

## Observation

In a real site-maintenance scenario the guide correctly sets outsideCore=true but then returns mostly TYPO3 Core changelog/Gerrit/runTests.sh advice. It also strongly classifies the task as Patch submission merely from ordinary project-maintenance wording. The MCP cannot inspect composer.json/composer.lock, identify the installed Core version, compare it with current patch releases, rank version-specific changelog entries against custom extension code, run or recommend project-level fluid:analyze, upgrade:list, extension scanner, composer validate/audit/outdated, or review Site Set/project structure. These are the central tasks a TYPO3 site developer needs.

## Query

Maintain and further develop a TYPO3 v14 Composer site project; review project structure, updates, changelog, deprecations, features and best practices

## Suggestion

Add a site/extension-maintenance guide that reads the discovered Composer installation, reports exact TYPO3 and PHP versions, checks Composer metadata and project layout, maps installed-version-to-target-version changelog/deprecations to concrete custom-extension paths, and provides project-valid commands. Suppress Core-only Gerrit and runTests.sh output when outsideCore=true, and tighten the patch-submission intent classifier.
