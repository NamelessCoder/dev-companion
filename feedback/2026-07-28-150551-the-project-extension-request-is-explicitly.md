---
date: 2026-07-28T15:05:51+00:00
category: missing-knowledge
status: open
tool: typo3_task_guidetypo3_rule_lookup
---

# The project-extension request is explicitly outside this server's stated core-contribution scope,...

## Observation

The project-extension request is explicitly outside this server's stated core-contribution scope, but typo3_task_guide did not say so. It generated a generic core patch checklist and irrelevant checkExtensionScannerRst, cgl and e2e recommendations. For the core variant it recognized only generic docs/tests and provided no site-set structure, naming, dependency, settings-definition, TypoScript, validation or targeted test guidance. typo3_rule_lookup matched only a generic Common Commands section at 50% coverage. The checkout contains many authoritative examples under typo3/sysext/*/Configuration/Sets and focused tests under typo3/sysext/core/Tests/Functional/Site/Set, none of which the answer surfaced.

## Query

Create a new TYPO3 site set in a project extension with config.yaml, settings.definitions.yaml and TypoScript dependencies; alternatively add a reusable site set to TYPO3 core

## Suggestion

Detect project paths such as packages/* and clearly route them out of scope instead of producing core checks. If core site sets are intended coverage, add a Site Sets architecture topic covering Configuration/Sets/<name>/config.yaml, identifier/label/dependencies, optional settings.definitions.yaml/settings.yaml/setup.typoscript/constants.typoscript/route-enhancers.yaml, existing core examples, and targeted SetRegistry/YamlSetDefinitionProvider tests. Otherwise state explicitly that the server cannot guide site-set creation and point to TYPO3 documentation.
