---
id: R-KNW-053
title: 'The per-class database answer says what survives the run'
status: held
restsOn: [D-KNW-022]
heldBy:
  - HintsTest::thePerClassDatabaseAnswerSaysWhatSurvivesTheRun
---

# R-KNW-053 — The per-class database answer says what survives the run

**The statement that every test class gets a database of its own says what
becomes of that database, and what tells one from the live database.**

Without them it describes databases appearing and nothing that says which of
them the harness will reclaim. A session reading it watches the set grow, cannot
tell a leftover from the database the site runs on, and starts accounting for
records by hand. So three things stand with it: nothing drops one when the run
ends and the next run of that class is what reclaims it, the suffix is a hash of
the test class rather than of the run so the set is bounded by the classes, and
that suffix is what marks a test database — with the two cases that carry no
such name, `$initializeDatabase = false` and `pdo_sqlite`.

## From

A TYPO3 14 testimonials session that accumulated a database per functional test
class over a working session and lost track of which records it had modified by
hand across the live database and the test ones — "created tons of databases,
lost track what manually were modified instead of using api",
`feedback/2026-08-01-003929` (2026-08-01).
