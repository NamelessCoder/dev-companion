---
id: R-KNW-043
title: 'A datamap answer says what the parent column holds'
status: held
restsOn: [D-KNW-018]
heldBy:
  - HintsTest::aRelationInADatamapSaysWhatTheParentColumnEndsUpHolding
---

# R-KNW-043 — A datamap answer says what the parent column holds

**The datamap answer says what a relation field's value is, what `foreign_field`
writes onto the child, and what the parent's own column ends up holding.**

Seeding advice that stops at the scalar field stops one field short of the work.
An element with inline children reaches a relation on the first record, and the
answer either continues there or the session invents the rest.

What the parent column holds is the half that reads as a defect. It is an
integer where a list was expected, which looks like a column that rejects a
comma list and is a counter DataHandler maintains — so the repair a session
reaches for, writing the child's pointer column and the parent's number itself,
is work the datamap had already done.

## From

A session seeding a sysfolder, two groups, five testimonials and a content
element through raw INSERT statements: it wrote the children's parent column by
hand after reading the parent's int column as one that rejects a comma list, and
lost track of which records it had touched (2026-08-01).
