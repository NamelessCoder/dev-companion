---
date: 2026-07-29T10:05:51+00:00
category: wrong-answer
status: open
tool: typo3_rule_lookup
---

# Both queries return backend CSS material -- "Class Naming" and "Component Structure" from the CSS...

## Observation

Both queries return backend CSS material -- "Class Naming" and "Component Structure" from the CSS architecture document, about Sass partials, BEM and t3js-* hooks -- labelled "matches 75% of the query terms". Neither answer contains anything about site sets, settings definitions, or content elements. The retrieval appears to be bare term overlap, and generic words (content, element, structure, definitions) pull the CSS corpus in. The damaging part is that the server holds the right answer: typo3_architecture_lookup with task "maintain and extend a TYPO3 v14 site package" returns an excellent Site Sets section that names settings.definitions.yaml as the replacement for TypoScript constants. So this is a routing failure, not a knowledge gap, and the confident percentage framing makes the wrong corpus read as a deliberate hit rather than a near-miss.

## Query

{"query":"site set settings definitions"} and {"query":"content blocks custom content element"}

## Suggestion

Weight query terms by how discriminating they are across the corpus so that "site set" and "settings definitions" outrank incidental matches on "content"/"structure", and let typo3_rule_lookup fall through to the architecture-hint corpus that typo3_architecture_lookup searches -- a caller asking about site sets should not have to know which of the two tools indexes them. Below a relevance floor, say "nothing matched well; the closest entries are ..." instead of presenting a low-confidence hit as a percentage match.
