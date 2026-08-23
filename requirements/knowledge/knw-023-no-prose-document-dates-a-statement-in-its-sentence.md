---
id: R-KNW-023
title: 'No prose document dates a statement in its sentence'
status: held
heldBy:
  - KnowledgeTest::noProseDocumentDatesAStatementInItsSentence
---

# R-KNW-023 — No prose document dates a statement in its sentence

**The prose documents are held to the same rule as the hints.**

A statement dated in its sentence cannot be filtered, and in markdown there is
no field to move the date into.

So the subject moves to the hint corpus rather than the sentence being reworded
— a version-bound statement is evidence that it was filed in the wrong corpus.
This is the version binding applied to the half of `knowledge/` the binding
cannot reach, and it is what decides what may stay as prose at all.

## From

«Since TYPO3 v14.1 a label marked that way raises an `E_USER_DEPRECATED`» in
`core/contribution/rules.md`, handed unqualified to a caller on 13.4 by
`typo3_rule_lookup`, which has no `targetVersion` and searches every document
(2026-07-30).
