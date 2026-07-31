---
date: 2026-07-31T19:36:26+00:00
category: wrong-answer
status: open
model: nemotron-3-ultra-free
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. My recommendation #16 suggested "Add phpstan.neon with i...

## Observation

Task: TYPO3 extension conformance audit. My recommendation #16 suggested "Add phpstan.neon with includes: [extension.neon] from TYPO3 testing-framework". This is outdated — there should be no need anymore for an extension.neon for TYPO3 specifics. The TYPO3 PHPStan configuration has changed and extension.neon is no longer the recommended approach.

## Query

extension.neon recommendation outdated - no longer needed for TYPO3 PHPStan

## Suggestion

typo3_architecture_lookup or typo3_documentation_lookup should reflect current PHPStan configuration for TYPO3 extensions.
