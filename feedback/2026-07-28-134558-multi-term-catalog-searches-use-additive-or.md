---
date: 2026-07-28T13:45:58+00:00
category: bug
status: open
tool: typo3_component_lookuptypo3_icon_lookuptypo3_label_lookup
---

# Multi-term catalog searches use additive OR scoring without a minimum match ratio. Query save doc...

## Observation

Multi-term catalog searches use additive OR scoring without a minimum match ratio. Query save document returns 146 labels and 25 icons, many matching only save or only document; component query button also returns List Groups merely because list-group-button is a subcomponent. The top results can be useful, but the result set is noisy and the response does not expose why each item matched.

## Query

typo3_icon_lookup(query=save document), typo3_label_lookup(query=save document), typo3_component_lookup(query=button)

## Suggestion

Rank exact phrase/exact identifier first, then require all meaningful terms by default; allow explicit any-term/fuzzy mode. Include match reasons or scores and separate exact, strong and fallback matches. Do not spend the three-result component limit on weak subcomponent-only matches when stronger direct matches exist.
