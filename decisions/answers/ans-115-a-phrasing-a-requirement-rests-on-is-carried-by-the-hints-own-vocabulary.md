---
id: D-ANS-115
title: "A phrasing a requirement rests on is carried by the hint's own vocabulary"
date: 2026-08-27
status: open
coveredBy:
  - HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun
---

# D-ANS-115 — A phrasing a requirement rests on is carried by the hint's own vocabulary

**A phrasing something else depends on is written into that hint's `appliesTo`,
because coverage admission is measured against weights every other hint in the
corpus decides.**

The two ways past `MIN_COVERAGE` differ in what they are a property of. A
matched pattern is the hint's own and changes only when somebody edits that
hint; a coverage share is computed from term weights taken over all the
candidates, so every statement written anywhere in the corpus is one of its
authors. A requirement resting on the second kind is held by hints nobody
writing it will ever read.

## Evidence

- `R-KNW-053`'s query "live database versus test database" stood at a coverage
  of 0.5016 against the floor of 0.500, with no `appliesTo` pattern matched. One
  further hint carrying "database" among the 108 candidates puts it under.
- That is what happened on 2026-08-26: an unrelated hint written into
  `knowledge/hints/php.json` used the word "test", which took that term from 45
  carriers to 46 and its weight from 0.876 to 0.854, and
  `HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun` failed. The
  statement that moved the weight was in neither the assertion nor the failure.
- The requirement's two other queries never reach the floor. "what happens to
  the test databases after the run" matches the pattern of the same name and
  "clean up functional test databases" matches "functional test", so both are
  admitted before coverage is asked.
- "versus" is carried by no hint at all, so it weighs `log(108) / 2` = 2.34 —
  35% of that query's weight, which nothing can ever cover. The floor was being
  asked to clear a bar two thirds of the query had to reach.
- Swept over the 58 query literals `HintsTest` writes out: 92 (query, hint)
  pairs are admitted by coverage alone, and three of them fall below the floor
  when one further hint carries one word of the query. Only `R-KNW-053`'s is a
  pair an assertion names; the other two are hints their query's assertion says
  nothing about.

## Decided

- `project-extension-tests` carries "test database", so the phrasing `R-KNW-053`
  rests on is admitted by the curated vocabulary at 13 rather than by a coverage
  margin of 0.0016. It is content rather than a repair: telling a test database
  from the live one is what `D-KNW-022` wrote the statement for.
- "live database" was the other candidate and was rejected. A question about a
  production dump names it too, and it would reach a hint about setting up a
  test suite.
- Lowering `MIN_COVERAGE` was rejected. The floor is what keeps a hint that
  mentions a subject from answering for it, and moving it for one query
  re-decides all 92 pairs above.
- Rewording the query was rejected. It is the phrasing the feedback behind
  `D-KNW-022` arrived in, and a test that asks an easier question than the
  caller does holds nothing.
- `bin/cli hints:probe` prints what each term of the query weighs and, per hit,
  which of the three ways in admitted it and how far the coverage stands from
  the floor. A session that has just written a hint reads there which word it
  made cheaper, which is what the failing assertion could not say.

## Assumed

- The 58 query literals are representative of what `HintsTest` holds. Queries
  that reach `find()` through a variable or a data provider were not swept, and
  a thin margin among them would look exactly like this one.
- Admission is the fragile part. A weight shift also reorders what was admitted,
  so an assertion naming the first hit can break while every hint stays
  admitted, and nothing here measures that.

## Wrong if

- A hint written into the corpus makes a reachability assertion in `HintsTest`
  fail again, and the query it fails on is one an `appliesTo` pattern matches.
- "test database" brings `project-extension-tests` into an answer about a
  production database.
