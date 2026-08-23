---
id: R-KNW-045
title: 'Reading records is covered as its own subject'
status: held
heldBy:
  - HintsTest::readingRecordsIsAnswered
---

# R-KNW-045 — Reading records is covered as its own subject

**How a record is read is answered as its own subject: what a QueryBuilder
restricts without being asked, and what is overlaid after the query rather than
selected in it.**

Both fail the same way — the record is in the database and not in the result —
and neither is visible in the SQL that was written.

## From

A corpus in which `datahandler-persistence` carried `querybuilder`,
`restriction`, `enablecolumns`, `hidden record` and `deleted record` in its
`appliesTo` and not one statement about reading. A grep for the reading APIs
over every hint there is returned one sentence, and it was about the doktypes of
a menu (2026-08-03).
