---
id: R-KNW-070
title: 'A relation value says which placeholder spelling survives it'
status: held
restsOn: [D-KNW-081]
---

# R-KNW-070 — A relation value says which placeholder spelling survives it

**Where a caller invents a string the core parses, the answer says which
spellings survive that parser.**

A placeholder is free-form at the call site and two fields to the code that
reads it, and nothing in the API the caller is handed says which of the two it
is. The spelling is therefore not a detail of the example: it is the part the
caller has to be told, because the wrong one costs a relation and reports
nothing.

## From

A session seeding a demo site named its inline children `NEW_card1` and lost
every relation: the children were written, the parent's counter stayed 0,
`uid_foreign` stayed 0, `process_datamap()` reported success and the error log
was empty. Twelve tool calls and three probe scripts isolated the underscore
(2026-08-17).

## Held by

- `HintsTest::aRelationValueSaysWhichPlaceholderSpellingSurvivesIt`
