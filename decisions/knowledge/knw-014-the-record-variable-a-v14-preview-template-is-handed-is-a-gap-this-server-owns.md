---
id: D-KNW-014
date: 2026-08-02
status: open
---

# D-KNW-014 — The record variable a v14 preview template is handed is a gap this server owns

**What a TYPO3 14 backend preview template can read off `{record}` is inside
this server's boundary and missing from it, so the feedback is queued.**

The corpus says where a preview template is registered and stops there. A
session that follows it arrives at a template with one variable in it, and
nothing here says what that variable is or what comes back from a field on it.

## Evidence

- The miss reproduces. Called over stdio with the feedback's own query and
  `targetVersion: "14"`, `typo3_architecture_lookup` returns one hint —
  `fluid-templates` — and no statement in it names a record variable.
  `bin/cli hints:probe` on the same query reaches the same single hint.
- Nothing below `knowledge/` or `skills/` contains `RecordInterface`,
  `{record.` or `LazyRecordCollection`. The entry the subject belongs to is
  `content-elements`, whose one preview statement gives the page TSconfig key
  `mod.web_layout.tt_content.preview.<CType>` and the TypoScript beside it. It
  carries no version range, so it also cannot say that v14 changed what the
  template is handed.
- The feedback's claims about TYPO3 hold. `.checkouts/14.3` has
  `Documentation/Changelog/14.0/Breaking-92434-UseRecordAPIInPageModulePreviewRendering.rst`,
  and `Core\Domain\Record` declares only `get()` and `has()`, inherited from
  `Psr\Container\ContainerInterface` through `RecordInterface` — no `__get`,
  no `ArrayAccess`.
- The version boundary is readable in the checkouts, which is what a statement
  needs to carry a `since`. `FluidBasedContentPreviewRenderer` calls
  `assignMultiple($row)` and then assigns `record` on 13.4; on 14.3 it assigns
  `record` alone.
- The mechanism the session could not find is readable too, and not from this
  repository: in typo3fluid/fluid 5.3.1 — the release 14.3 requires —
  `StandardVariableProvider::getByPath()` resolves a path segment on a
  `ContainerInterface` through `has()` and `get()`, ahead of every getter it
  tries. Read at that version in a local installation's `vendor/`, because no
  core checkout has one.
- The relational half is establishable from the same checkout rather than from
  an installation, so this is queued and not blocked.
  `Core\DataHandling\RecordFieldTransformer` is where a field value becomes
  what the template sees, and `theme_camino` ships preview templates that
  iterate a relation with `f:for` — `ContentPreviews/Linklist.fluid.html`.
  Which field types reach that branch is the todo's reading, not this one's.

## Decided

- Step 1a of the ladder, and queued. Content elements are work this server
  answers for, the statement is bound to a major, and there is nothing here to
  reword or move.
- Not step 3. Routing works where the query is spelled the way the corpus
  spells it: `tt_content preview template` reaches `content-elements` at
  `appliesTo(10) + text(129)`. What the caller's own spelling does to the same
  query is [`D-ANS-022`](../answers/ans-022-a-hyphenated-compound-reaches-neither-the-phrase-nor-the-word.md).
- Not closed on the spot. Every statement has to be read on 13.4 and 14.3 and
  against the Fluid release each of them pins, and this run has read nothing
  but the greps above.

## Assumed

- The statement belongs on `content-elements` rather than on
  `fluid-templates`. What a preview template is handed follows from the CType
  it previews, and a session writing one arrives there from the registration.

## Wrong if

- What the todo establishes is Breaking-92434 restated. The entry is then a
  pointer to `typo3_changelog_lookup` on `content-elements` rather than a
  statement of its own.
- What a field resolves to turns out to depend on the TCA of that field rather
  than on the Record API, so no single statement holds for "a relation in a
  preview". The hint then has to name which field types resolve to records and
  which stay a string, or say nothing.

## Since then

A second gap on the same statement was judged on 2026-08-02 and is queued beside
this one: what a Fluid preview template *replaces* is the content half of the
preview only, and the header the standard renderer draws stays above it —
[`D-KNW-015`](knw-015-a-fluid-preview-template-replaces-the-content-half-and-nothing-here-says-so.md).
Both todos rewrite the one preview statement on the `content-elements` hint, so
whichever lands second rewrites in place rather than adding beside. The two
statements differ in what binds them: what `{record}` is changed in 14 and needs
a `since`, while the header/content split reads the same on 13.4 and 14.3.
