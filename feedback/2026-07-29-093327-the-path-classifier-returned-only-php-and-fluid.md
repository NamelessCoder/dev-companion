---
date: 2026-07-29T09:33:27+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
---

# The path classifier returned only php and fluid, although the task explicitly includes TypoScript...

## Observation

The path classifier returned only php and fluid, although the task explicitly includes TypoScript and the extension contains Configuration/Sets/*/setup.typoscript. It returned useful generic TCA, Fluid and core-test hints but nothing specific for frontend DataProcessors, extension upgrade wizards, TYPO3 13/14 cross-version compatibility, site sets, or third-party extension testing. Several hints are phrased as core-only requirements even though the supplied paths are not below typo3/sysext.

## Query

Paths: Classes/DataProcessing/CsvFileProcessor.php, Configuration/TCA/Overrides/102_tt_content.php, Resources/Private/Templates/ContentElements/Accordion.html, Classes/Updates/AccordionContentElementUpdate.php; task: extension compatibility upgrade, TCA migration, Fluid templates, DataProcessors and upgrade wizards

## Suggestion

Extend domain/path classification for common extension layouts (Configuration/Sets, setup.typoscript, Classes/DataProcessing, Classes/Updates, Resources/Private/Templates). Add version-aware extension-maintenance guidance for public API/deprecation checks, upgrade wizard idempotence, TCA compatibility, DataProcessor contracts and site-set validation, or clearly return an unsupported-domain gap instead of generic core guidance.
