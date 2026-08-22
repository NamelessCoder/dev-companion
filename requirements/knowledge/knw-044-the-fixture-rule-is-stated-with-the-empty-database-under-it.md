---
id: R-KNW-044
title: 'The fixture rule is stated with the empty database under it'
status: held
restsOn: [D-KNW-019]
---

# R-KNW-044 — The fixture rule is stated with the empty database under it

**A functional test's database holds nothing but what that test primed, and the
knowledge base states that beside the fixture rule resting on it.**

Without the premise the CSV fixture rule reads as one convention among several,
achievable another way. A session that reads it so fetches records nobody
primed, and the failure looks like a broken query rather than an empty table. So
the premise is stated where the rule is, in the words a caller asks it in — an
empty database per test run — and with the two boundaries that would make it too
strong unqualified: `$initializeDatabase = false` and
`withDatabaseSnapshot()`.

## From

A TYPO3 14 testimonials session that repeatedly did not understand the
functional test data model: it kept fetching and verifying data that was never
primed, resorted to inserts on the live database, and had to be prompted with
"is your dataset correct?" — `feedback/2026-08-01-003003` (2026-08-01).

## Held by

- `HintsTest::theFixtureRuleIsStatedWithTheEmptyDatabaseUnderIt`
