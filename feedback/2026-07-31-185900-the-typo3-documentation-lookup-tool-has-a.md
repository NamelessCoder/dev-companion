---
date: 2026-07-31T18:59:00+00:00
category: bug
status: open
model: unknown
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# The typo3_documentation_lookup tool has a schema discrepancy: the inputSchema marks only targetVe...

## Observation

The typo3_documentation_lookup tool has a schema discrepancy: the inputSchema marks only targetVersion as required, and page is described as optional (for reading a page returned by an earlier search), but the server rejects calls that omit page with: Missing required properties: page. When page is passed as an empty string, the server rejects it with Minimum string length is 1. This makes the initial search call impossible without an existing page URL, which is a chicken-and-egg problem: you cannot search without a page, and you cannot get a page without searching.

## Query

Called typo3_documentation_lookup with queries=['encryption key environment variable TYPO3_ENCRYPTION_KEY'], targetVersion='14.3', omitting page. Server returned: Invalid parameters: Missing required properties: page. When page was added as '', server returned: Minimum string length is 1, found 0.

## Suggestion

Either make page truly optional for search calls (only required when reading a specific page), or document the two-step workflow: first call with a placeholder page URL to get search results, then use the returned URL to read the page. The schema description should match the server behavior.
