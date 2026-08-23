---
id: R-SCO-006
title: 'Every topic says which kind of work it is for'
status: held
restsOn: [D-KNW-005]
heldBy:
  - KnowledgeTest::everyScopeInTheCorpusIsOneTheEnumDeclares
  - ScopeTest::everyCoveredTopicSaysWhatItIsWorthOutsideTheCore
---

# R-SCO-006 — Every topic says which kind of work it is for

**Every covered topic states which kind of work its answers are for, as a
`Scope`: `core` or `any`.**

The boundary runs through the middle of this server rather than around it, and a
caller that has to work that out per tool ends up trusting all of it or none of
it.

Where an answer is read from is a different question and `source` already
answers it, so `installation` stopped being a value of this field on 2026-08-02
([`D-KNW-005`](../../decisions/knowledge/knw-005-one-scope-replaced-the-four-vocabularies.md)).

## From

A site developer for whom five installation-backed tools answered correctly
while the curated half handed over runTests.sh commands, with nothing in the
scope separating the two (2026-07-29).
