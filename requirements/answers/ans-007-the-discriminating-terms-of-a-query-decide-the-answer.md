---
id: R-ANS-007
status: held
---

# R-ANS-007 — The discriminating terms of a query decide the answer

**A query is scored by the terms that separate one section from the rest, not by
term overlap.**

A word half the knowledge base carries decides nothing, a term is matched as a
word rather than as a substring, and which of the two corpora — the prose or the
hints — holds a subject is not the caller's problem: `typo3_rule_lookup` names
the hints that match the same query.

## From

"site set settings definitions" answered with the backend's Sass class naming,
at a stated 75% of the query terms (2026-07-29).

## Held by

- `KnowledgeTest::theDiscriminatingTermsOfAQueryDecideTheAnswer`
- `KnowledgeTest::aTermMatchesAWordRatherThanAnythingThatContainsIt`
- `ScopeTest::aRuleQueryIsPointedAtTheHintCorpusItBelongsIn`
