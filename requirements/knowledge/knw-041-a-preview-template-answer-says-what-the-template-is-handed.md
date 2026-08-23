---
id: R-KNW-041
title: 'A preview template answer says what the template is handed'
status: held
restsOn: [D-KNW-020]
heldBy:
  - HintsTest::aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo
---

# R-KNW-041 — A preview template answer says what the template is handed

**A backend preview answer names the variables the template receives on the
target major, and what a field read off the record resolves to.**

Registering the template is where the corpus used to stop, and it is the half a
session does not need help with. The other half is bound to the major: the row's
columns are variables of their own until the Record API replaces them, and after
that everything is read off one record.

What a field resolves to is decided by the TCA type of that field, so the answer
names which types come back as records — select with a relation, group, inline,
category, file — and what a static select stays instead. The two ways the
template silently renders nothing are stated with them: a column read as a bare
variable where only the record is assigned, and a path that is not a schema
field of the record type, which resolves to null rather than raising.

## From

A session writing a TYPO3 14 preview template that had to show assigned related
groups: it found the registration and nothing else, could not determine how
{record.header} resolves given a class with neither `__get` nor `ArrayAccess`,
and guessed a template it could not verify (2026-08-01).
