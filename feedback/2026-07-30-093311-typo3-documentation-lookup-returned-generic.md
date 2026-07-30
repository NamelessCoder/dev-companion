---
date: 2026-07-30T09:33:11+00:00
category: missing-knowledge
status: open
tool: typo3_documentation_lookup
---

# typo3_documentation_lookup returned generic custom-content-element documentation when asked for T...

## Observation

typo3_documentation_lookup returned generic custom-content-element documentation when asked for TYPO3 14 FunctionalTestCase frontend rendering, executeFrontendSubRequest, and CSV fixture guidance. The result did not help verify the test APIs used by the project.

## Query

FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14; functional testing custom content element rendering TYPO3 14

## Suggestion

Add query aliases or curated, version-bound testing guidance for FunctionalTestCase, importCSVDataSet, SiteWriter, InternalRequest, and executeFrontendSubRequest. If official docs do not cover these APIs, say so directly and route to the project-extension testing architecture hint instead of returning generic content-element pages.
