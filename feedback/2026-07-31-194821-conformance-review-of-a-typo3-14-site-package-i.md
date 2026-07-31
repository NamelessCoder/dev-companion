---
date: 2026-07-31T19:48:21+00:00
category: idea
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# Conformance review of a TYPO3 14 site package: I needed to know whether the TypoScript-based form...

## Observation

Conformance review of a TYPO3 14 site package: I needed to know whether the TypoScript-based form YAML registration (deprecated in 14.2) is removed in v15 before judging the package's form-set approach as future-proof. The changelog_lookup answer carried the title and a pointer to read the .rst for the migration, but not the removal target; I had to open vendor/typo3/cms-core/Documentation/Changelog/14.2/Deprecation-109412-FormYamlConfigurationRegistration.rst to learn "removed in TYPO3 v15.0".

## Query

typo3_changelog_lookup query="yaml" version="14.2", then vendor .rst read for the removal version

## Suggestion

For deprecation entries, include the removal version ("will be removed in vX") in the answer. It is the deciding fact for audits and upgrade planning, not a detail.
