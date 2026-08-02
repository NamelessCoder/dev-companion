---
id: D-ANS-026
date: 2026-08-02
status: open
---

# D-ANS-026 — The ViewHelper reference is indexed, and a manual carries the collection it is published in

**`typo3_documentation_lookup` searches four manuals, and where one is
published is part of what the index knows about it.**

[`D-ANS-023`](ans-023-no-viewhelper-is-documented-in-any-manual-this-lookup-indexes.md)
is the finding: three books, none of which documents a ViewHelper. This entry is
what took its place — the book is carried, and carrying it cost the one thing
every base used to have in common.

## Evidence

- The reference is published like the others and versioned like them.
  `https://docs.typo3.org/other/typo3/view-helper-reference/<version>/en-us/`
  answers 200 on 12.4, 13.4, 14.3 and main — the four branches
  `knowledge/versions.json` covers — so the version asked for is the branch that
  is already asked for elsewhere and the engine keeps no axis of its own
  ([`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)).
- Its root is a table of contents `Documentation::links()` reads: 189 pages at
  14.3, `Global/If.html` among them. That was the first **Wrong if** of
  `D-ANS-023` and it did not hold.
- The pages excerpt. The If page opens with "This ViewHelper implements an
  if/else condition"; the Then page is 184 characters and still says what it is
  for.
- What differs from the three of the core is the base and nothing else.
  Every one of them is `/m/<document>/<version>/en-us/`; this one is `/other/`.
- One question that merely says Fluid is not taken over by it. Measured live at
  14.3, `Fluid template layout partial section` still puts Multi-language Fluid
  templates first, with the `section` and `layout` pages second and fifth. All
  189 pages carry "Fluid" in the `manual` field, and a term everything carries
  separates nothing.
- The other manuals keep their questions: TCA `inline`, the TypoScript content
  objects and the functional-testing pages come back as before.

## Decided

- A manual is `{title, collection}` and one method builds every base from it.
  The collection is what says where a manual is, rather than a constant of the
  host, so a fifth book is one entry in whichever collection publishes it.
- The book keeps the title its publisher gives it, "Fluid ViewHelper Reference",
  measured rather than feared — see the fifth piece of evidence.
- What has to keep holding is
  [`R-DOC-003`](../../requirements/documentation/doc-003-a-viewhelper-question-is-answered-from-the-manual-that-documents-viewhelpers.md),
  and the two tests under **Covered by** hold it.
- Not decided here: the tokenizer. `f:if` reaches `Global/Else.html` and not
  `Global/If.html`, because `TermSearch::terms()` drops every word under three
  characters and `then` is a stopword. The lever is `TermSearch`, which the hint
  and prose corpora go through as well, so it carries a todo of its own.

## Assumed

- The book stays where it is published: same collection, same slug, same
  versions the covered releases are asked for.
- One book more is worth one fetch more per call. The lookup already fetches
  every indexed root before it scores anything.
- What was measured on one afternoon holds for questions nobody asked that day.

## Wrong if

- `docs.typo3.org` moves the book — another collection, another slug, one
  version that is not published. A base that is wrong is silent: the root does
  not answer, the book is absent from the index, and the answers go back to what
  `D-ANS-023` recorded with nothing failing anywhere.
- Its short titles start winning questions that are not about a ViewHelper. What
  says they do not is a handful of queries at one version, not a suite.
- The single-file page becomes what a search pays for. Every manual root links
  `singlehtml/Index.html`, the whole book as one page, and `isDocumentPage()`
  lets it into the index as an ordinary candidate; for this book it is 2.9 MB,
  where in the three under `/m/` it has been cheap enough to go unnoticed.

## Covered by

- `DocumentationTest::aViewHelperQuestionReachesTheManualPublishedOutsideTheCoreCollection`
- `DocumentationTest::aPageOfThatManualIsReadBackAtItsOwnBase`
