---
id: R-KNW-073
title: 'A step that reads from a cache says what invalidates it'
status: held
restsOn: [D-KNW-089]
---

# R-KNW-073 — A step that reads from a cache says what invalidates it

**Where a procedural step reads its input from a cache, the hint says what
invalidates that cache and what the step does when it is stale.**

The rule without the precondition reads as a habit, so a caller who has the
order right runs the step against yesterday's input — and a step whose success
message is unconditional then reports the same success it would have reported
for the work it skipped.

## From

`typo3 extension:setup` run after two TCA files were added to an active package:
the schema step migrated from the TCA cached before them, created neither table
and answered `[OK] Extension(s) ... successfully set up.`, and the seeding
script that followed died on a table that does not exist
(feedback/2026-08-17-212117, 2026-08-17).

## Held by

- `HintsTest::theSchemaStepIsSaidToMigrateFromTheCachedTca`
