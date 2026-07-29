---
date: 2026-07-29T09:33:02+00:00
category: tool-gap
status: open
tool: typo3_task_guide
---

# The guide correctly marks the task as outsideCore, but still emits a core-oriented checklist and ...

## Observation

The guide correctly marks the task as outsideCore, but still emits a core-oriented checklist and Build/Scripts/runTests.sh commands. Those commands do not exist in this extension checkout; its composer.json exposes composer test, test:php:unit, test:php:functional, test:php:lint, phpstan and cgl:ci. For an extension maintainer this is actively misleading and omits the repository-native verification workflow.

## Query

Maintain and extend the third-party TYPO3 extension bk2k/bootstrap-package for TYPO3 13.4 and 14.3: review TCA, TypoScript, Fluid templates, data processors and upgrade wizards for compatibility and choose tests

## Suggestion

When outsideCore is true, return transferable conventions separately and suppress core-only commands. Add an extension/project mode that can accept repository-provided scripts or at minimum instruct the client to inspect composer.json/package.json/CI and run those commands. Include the source of each recommendation and an applicability flag such as core-only/transferable/project-derived.
