---
id: R-KNW-058
title: 'The placement answer says which page may hold the record'
status: held
restsOn: [D-KNW-023]
---

# R-KNW-058 — The placement answer says which page may hold the record

**The placement answer says which page may hold a record: the doktype decides,
and a folder is what allows a table of your own.**

The rest of the rule comes with it. A standard page allows `pages`,
`sys_category`, `sys_file_reference`, `sys_file_collection` and every table
declaring `ctrl.security.ignorePageTypeRestriction`; a `pid` of 0 is the table's
own `ctrl.rootLevel` instead, with admin or
`ctrl.security.ignoreRootLevelRestriction` on top.

Positioning is the second question and picking the pid is the first. A session
seeding a table of its own has to choose a page before it can order anything on
it, and the corpus answered only the ordering.

The failure it leaves is silent. DataHandler logs the refusal and continues, so
the datamap call returns without an error and the row is not there — which reads
as a write that did not happen for some other reason, and sends the session
looking at its field values.

The admin clause is part of the demand rather than a detail. A seeding script
runs as the CLI user, and an answer that leaves the doktype check sounding like
an editor restriction describes a check that session will meet anyway.

## From

A session seeding a sysfolder, two groups, five testimonials and a content
element: it guessed at the pid and at the storage folder's role, and nothing in
the corpus said which page may hold what (2026-08-01).

## Held by

- `HintsTest::thePlacementAnswerSaysWhichPageMayHoldTheRecord`
