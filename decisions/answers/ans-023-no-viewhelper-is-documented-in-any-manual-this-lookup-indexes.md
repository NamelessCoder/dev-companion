---
id: D-ANS-023
date: 2026-08-02
status: open
---

# D-ANS-023 — No ViewHelper is documented in any manual this lookup indexes

**`typo3_documentation_lookup` searches three books and none of them documents
a ViewHelper, so a question about `f:if` comes back with whatever prose carries
the word.**

One session lost a task to three separate Fluid mistakes and read them as one
systemic gap. It is one gap, and this is where it sits: the reference that
answers what a ViewHelper does is a manual of its own, published the way the
TCA reference is, and the lookup does not carry it.

## Evidence

- The miss reproduces. `bin/cli hints:probe` with the feedback's own query
  reaches one hint, `frontend-records`, which says nothing about Fluid.
  Narrowed to `fluid template conditional` it reaches `fluid-templates` at
  `appliesTo(14) + text(99)`. `IfViewHelper condition branch`,
  `iterate relation field f:for` and `typolink inside conditional` reach
  nothing at all.
- The corpus names `f:then` once, in the `until: 12` statement on
  `fluid-templates` about an array-typed argument. That statement is about what
  an `f:if` evaluates to as a value, not about which branch renders.
- The manual answers no better. Called over stdio at `targetVersion: "14"` with
  `f:if f:then f:else condition ViewHelper`, and again with `IfViewHelper` and
  `f:then`, the lookup returned the same four pages both times — Developing a
  custom ViewHelper, the Translate ViewHelper, JavaScript form helpers, and
  TSconfig Conditions.
- The book it should have returned exists and is versioned like the others.
  `https://docs.typo3.org/other/typo3/view-helper-reference/<version>/en-us/`
  answers 200 on 12.4, 13.4, 14.3 and main, with 341 to 401 anchors at each
  root, and one page per ViewHelper below it —
  `typo3fluid/fluid/latest/If.html` for this one.
- That page carries the `f:then` / `f:else` structure as its second example. It
  does not carry the trap: its Basic usage says everything inside the tag is
  displayed when the condition is true, and nothing there says what an `f:else`
  beside it does to that.
- The claim the feedback makes about TYPO3 holds. Read in `typo3fluid/fluid`
  5.3.1 — the engine 14.3 pins, [`D-VER-003`](../versions/ver-003-the-fluid-engine-gets-no-version-axis-of-its-own.md)
  — in a local installation's `vendor/`, because no core checkout has one.
  Uncached, `AbstractConditionViewHelper::renderThenChild()` walks the child
  nodes and returns `null` where it finds an `ElseViewHelper` and no
  `ThenViewHelper`. Compiled, `convert()` wraps the body as `__then` only when
  no `f:then`, `f:else` or `f:else if` child was seen, so the same template
  compiles with no then closure at all. Nothing raises and nothing is logged,
  which is the empty link the session reported.
- Adding the book is not one entry in an array. `Documentation::search()`
  builds every base as `/m/<document>/<version>/en-us/`, and this manual is
  published under `/other/`.

## Decided

- Step 1b of the ladder, and queued. The answer is available and cannot be had
  in the shape the task needed, and the lever is the index of
  `typo3_documentation_lookup` rather than a fourth hint. It touches `src/`, so
  it is not closed on the spot.
- The aggregate feedback gets the aggregate lever. What `f:if` does to the
  branch beside it is the whole subject of
  `feedback/2026-08-01-003448-specific-fluid-f-if-f-then-f-else-failure-a.md`,
  which is still unjudged and has its own card in `todo/open/`. The engine
  reading above is recorded here so that judgement does not have to repeat it,
  and so the statement it queues is written once.
- Not queued a second time: what a v14 preview template is handed, and whether
  a relation field is iterable, are already
  [`D-KNW-014`](../knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md)
  and the todo in hand for it. This feedback reports both and neither is worked
  again here.
- Not step 3, and not step 4. The query reaches the hint it should reach, and
  the skill the session had active names `typo3_documentation_lookup` for Fluid
  APIs. There is nothing here to move or reword.

## Assumed

- The reference's root is a table of contents that `Documentation::links()` can
  read, on the strength of the anchor counts alone. The parser was not run
  against it.
- One book more is worth one fetch more per call. The lookup already fetches
  every indexed root before it scores anything.

## Wrong if

- The anchors at that root turn out to be navigation rather than a page list,
  so indexing the book adds a request and no candidates.
- Indexing it makes the other answers worse. It is a large book of short
  titles, `FIELD_WEIGHTS` weighs `manual` at 2, and a question that merely says
  Fluid would then outrank the Fluid chapter of TYPO3 Explained.
- The pages come back too thin to excerpt. A ViewHelper page is largely
  argument tables and code, and the reader takes its excerpt from paragraphs.
