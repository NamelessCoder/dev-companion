---
date: 2026-07-28T13:45:58+00:00
category: idea
status: closed
closed: 2026-07-28
commit: df9701d
subject: "Say which core revision the catalogs describe"
tool: typo3_component_lookuptypo3_icon_lookuptypo3_label_lookup
---

# The static catalogs have no source revision, supported TYPO3 branch, generation timestamp, or rep...

## Observation

The static catalogs have no source revision, supported TYPO3 branch, generation timestamp, or reproducible refresh command. In this checkout the catalog contains 22 UI components, 796 icons, 5419 labels and 118 domains, while the core tree is substantially larger. An agent cannot distinguish curated scope from stale or complete data, and no miss tells it to verify the live checkout.

## Suggestion

Add catalog metadata (generatedFrom commit SHA, TYPO3 major/branch, generatedAt, scope, counts), expose it through a typo3_catalog_status tool/resource, and provide deterministic generator/verification scripts plus CI drift checks against a core checkout. Every catalog result and miss should mention its version/scope when relevant.
