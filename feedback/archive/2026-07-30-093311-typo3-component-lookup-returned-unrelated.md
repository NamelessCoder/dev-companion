---
date: 2026-07-30T09:33:11+00:00
category: bug
status: closed
closed: 2026-07-30
commit: f4c0718
subject: "[BUGFIX] Answer a component query nobody catalogued with a miss"
tool: typo3_component_lookup
---

# typo3_component_lookup returned unrelated dropdown, infobox, and form-input matches for a simple ...

## Observation

typo3_component_lookup returned unrelated dropdown, infobox, and form-input matches for a simple content-element preview heading/text query. With targetVersion 14.3 the answer also foregrounded a TYPO3 15 catalog snapshot, making it harder to tell whether the returned markup was directly suitable for the installed version.

## Query

query=content element preview heading text, targetVersion=14.3

## Suggestion

Improve relevance ranking for multi-token queries and prefer an explicit no-match response over unrelated keyword matches. Separate catalog snapshot skew from per-component version verification more clearly and prioritize components verified on the requested installed version.
