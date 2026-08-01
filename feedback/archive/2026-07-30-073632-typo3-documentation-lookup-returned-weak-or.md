---
date: 2026-07-30T07:36:32+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: f7fe932
subject: "[FEATURE] Search the manuals the way the rest of this server searches"
tool: typo3_documentation_lookup
---

# typo3_documentation_lookup returned weak or unrelated results for TYPO3 14 inline IRRE child reco...

## Observation

typo3_documentation_lookup returned weak or unrelated results for TYPO3 14 inline IRRE child records and Fluid AssetCollector ViewHelpers.

## Query

TCA inline foreign_field foreign_sortby localization children; Fluid AssetCollector css javascript ViewHelper

## Suggestion

Add targeted query aliases or curated architecture hints for inline child persistence and frontend AssetCollector usage, verified against covered TYPO3 versions.
