---
id: D-FBK-043
title: A structure is answered with a document rather than with a rule
date: 2026-08-04
status: open
---

# D-FBK-043 — A structure is answered with a document rather than with a rule

**Where a feedback found a structure unclear rather than a statement, the answer
is a document: `knowledge/documents/` where the caller needed it,
`documentation/` where a session here did.**

The three directories a judgement writes into carry a rule, a rationale and a
next step. A procedure whose shape is what is missing fits none of them, and
written as a requirement it becomes one sentence saying the shape should be
clear.

## Evidence

- The two pages disagreed. Step 1a of the ladder ends in prose being established
  and written, while "Where the answer goes" named three directories, and none
  of them is where prose lands.
- The corpus grew documents in a week the workflow never mentioned them.
  `knowledge/documents/` holds `core/contribution`, `core/testing`,
  `extension/testing` and `project/testing`, and six commits of 2026-08-03 and
  2026-08-04 added, moved or rewrapped one.
- What a document is was settled in the same days and in another group:
  [`D-KNW-056`](../knowledge/knw-056-a-file-skeleton-is-shipped-as-a-version-bound-document-section.md)
  for the bound section,
  [`D-KNW-057`](../knowledge/knw-057-a-document-declares-what-it-is-and-when-to-reach-for-it.md)
  for what it declares,
  [`D-KNW-059`](../knowledge/knw-059-one-place-spells-how-a-document-is-addressed.md)
  for how it is addressed. The judging page could reach none of it.
- A document is the only one of the four a caller reads. It is searched by
  `typo3_rule_lookup` and served as a resource; a requirement, a decision and a
  todo are read on this side alone.

## Decided

- A fourth destination for what a judgement establishes, in two halves told
  apart by who was lost: the corpus where the caller was, `documentation/` where
  a session working here was.
- A `documentation/` page may be written in the judging run, under the test
  *closed on the spot* already sets — no contract moves and nothing about TYPO3
  is looked up. It describes this repository, which that run has just read.
- A `knowledge/documents/` page is *taken on* instead. It states what holds
  about TYPO3, so the reading is the todo's first step, and the judgement
  decides only that the document exists and what it covers.
- The seven answers stay seven. This says where what was established lands, not
  what becomes of the feedback, and the invariant is unchanged: the commit
  archives it or leaves a todo serving it.

## Assumed

- That a judging run can tell a structure from a statement. The distinction is
  whether the answer is one sentence somebody follows or a thing they copy, and
  it is read off the feedback rather than off the category.
- That a procedure carried out here is safe to write from a judgement, because
  the evidence for it is in this checkout and nowhere else.

## Wrong if

- A document written in a judging run states something about TYPO3 that the
  reading would have corrected. That is the guess with a reading's authority the
  ladder exists to prevent, now with a published page behind it.
- Documents accumulate that nothing routes to — a page in the corpus that no
  `covers` entry names and no hint reaches is one nobody can find, and the
  judgement that wrote it recorded nothing.
- The three directories stop being reached, because prose is the easier thing to
  write: a rule that belonged in `requirements/`, where a test could hold it,
  ends up as a paragraph nothing checks.
