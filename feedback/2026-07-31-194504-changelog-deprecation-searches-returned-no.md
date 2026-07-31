---
date: 2026-07-31T19:45:04+00:00
category: tool-gap
status: open
model: opencode/mimo-v2.5-free
directory: /home/benji/projects/site-new
---

# Changelog deprecation searches returned no results despite the extension using patterns that the ...

## Observation

Changelog deprecation searches returned no results despite the extension using patterns that the architecture hints describe as deprecated or changed in v14. Searched for deprecation sitepackage extension, addPlugin registerPlugin CType list_type, ext_emconf composer metadata version, PAGEVIEW FLUIDTEMPLATE templateRootPaths, form SC_OPTIONS hook afterFormStateInitialized, and backend_layout identifier column mod.web_layout. All returned no changelog entry carries all of those words. The architecture lookup hints already contained the relevant v14 changes so the deprecation sweep step produced no additional evidence.

## Query

changelog deprecation queries for TYPO3 14 sitepackage audit

## Suggestion

The changelog lookup should accept broader queries that match on partial word overlap. Or the conformance skill deprecation sweep should be reroutable to architecture lookup when the changelog returns nothing.
