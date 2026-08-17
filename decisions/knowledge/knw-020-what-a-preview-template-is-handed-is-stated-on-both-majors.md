---
id: D-KNW-020
date: 2026-08-02
status: open
---

# D-KNW-020 — What a preview template is handed is stated on both majors, and a field resolves by its TCA type

**The corpus states what a preview template is handed on each major, and what a
field read off the record resolves to.**

[`D-KNW-014`](knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md)
is the finding: a session arrived at a template with one variable in it and
nothing here said what that variable was. This entry is what took its place —
the statements are on `content-elements`, and what they can now be wrong about
is their content rather than their absence.

## Evidence

- The `content-elements` hint carries the assignment split by major.
  `since: 14`: the template "is handed one variable, record", and a template
  that reads `{header}` renders an empty spot and logs nothing. `until: 13`:
  beside `record` it is handed every column of the row and a plugin's FlexForm
  as
  `{pi_flexform_transformed}`.
- The resolution half carries `since: 13` rather than `since: 14`, because both
  majors assign the record. `.checkouts/13.4`'s
  `FluidBasedContentPreviewRenderer` calls `assignMultiple($row)` at line 95 and
  `assign('record', …)` at line 99; `.checkouts/14.3` assigns `record` alone at
  line 75.
- The mechanism the reporting session could not find is stated: the record is a
  PSR-11 container and Fluid asks `has()` and `get()` for a path segment before
  it tries any getter, so every field is `{record.<column>}`. What the schema
  does not declare is not on the record — the enable fields, the timestamps, the
  sorting and the language and workspace columns are
  `{record.systemProperties.disabled}` — and a path that hits neither resolves
  to null rather than raising.
- What a field resolves to is named by TCA type rather than as one rule for "a
  relation": `type=select` with a relation, `group`, `inline`, `category` and
  `file` come back as records, a relation to many as a lazy collection `f:for`
  iterates, and `type=select` without a relation stays values — the single value
  where `renderType` is `selectSingle`.
- The subject is reachable from the question it was missing for.
  `bin/cli hints:probe` on "Record API field access in a backend content element
  preview Fluid template" puts `content-elements` first at
  `appliesTo(15) + text(288)`, ahead of `fluid-templates`.

## Decided

- `D-KNW-014` is revoked in place rather than rewritten. Its statement was that
  the answer is missing here, which stopped being true the afternoon the
  statements landed, and neither of its **Wrong if** could fire against a gap
  that is closed.
- `R-KNW-041` now rests on this entry rather than on the revoked one. The
  reasoning under the requirement moved rather than went, and left pointing at
  `D-KNW-014` it reads out of `bin/cli unresolved:list` as a requirement whose
  ground is gone.
- The version split stays data on the statement — `since` and `until` — and no
  sentence names a major
  ([`D-VER-001`](../versions/ver-001-a-version-range-is-data-on-the-statement-not-a-sentence-in-it.md)).
- Not restated here: the reading the statements were written from. It is in
  `D-KNW-014`'s **Evidence** and its **Confirmed on**, which is what the revoked
  entry is kept for.

## Assumed

- The five types are the whole of what a preview template meets as a relation.
  They were read off the branch a field takes in `RecordFieldTransformer`, not
  by rendering a template over every TCA type.
- A session writing a preview template arrives from the registration, so
  `content-elements` is where it reads this rather than `fluid-templates` — the
  assumption `D-KNW-014` made, kept because the probe above bears it out.

## Wrong if

- A sixth field type starts resolving to records, or one of the five stops. The
  statement then names a set that is wrong, and a template written to it renders
  an empty spot the author only sees in the page module.
- A later major changes what is assigned again. The `since: 14` half then
  describes a boundary rather than the present, and a caller on that major is
  told the row's columns are gone when something else is.
- Fluid stops resolving a path segment through `has()` and `get()` ahead of the
  getters, or the core stops handing the template a container.
  `{record.<column>}` then works for another reason, and the sentence explaining
  why is right by accident.

## Covered by

- `HintsTest::aPreviewTemplateSaysWhatItIsHandedAndWhatAFieldResolvesTo`

## Since then

A second question about the same variable was judged on 2026-08-18 and is queued
beside this one: these statements say how a field is read off the record and
never what the record or the field is as a PHP type, which is what a typed
`f:argument` declares —
[`D-KNW-090`](knw-090-the-corpus-names-the-php-type-a-record-and-a-transformed-column-arrive-as.md).
Both land on `preview-record-variable`, so whichever is written second reads the
statements this one put there rather than adding beside them.

