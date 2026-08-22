---
id: D-GUI-005
title: The product premise is one statement, on the brief every task passes through
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::everyBriefOpensOnThePremiseADefectIsJudgedBy
---

# D-GUI-005 — The product premise is one statement, on the brief every task passes through

**What a CMS decides a defect by is stated once, as the first item of the
`typo3_task_guide` checklist, and the mechanisms it explains stay where they
already are.**

Three cards were about to say it three times — cache busting, cache
invalidation, the rendered preview — and none of the three is the reason a
session got the assessment wrong.

## Evidence

- The feedback. `feedback/2026-08-02-145043`, `claude-opus-5[1m]`, from
  `/home/benji/projects/typo3-cms`, `tool: typo3_task_guide`: "Every fact I
  needed was in the catalogue or the checkout. What was missing was the premise
  that decides which facts matter." Its own suggestion names this tool's bugfix
  checklist and the wording "assess the report by the outcome for editor and
  visitor before assessing the code that was written".
- The corpus does not state it. `bin/cli hints:probe` on 2026-08-03 with
  "content changes and what is delivered has to be the current version" reaches
  `form-framework` on 52 points of text alone, and with "judge a bug report by
  the outcome for the editor and the visitor" nothing at all, 76 hints returned
  as the index.
- The mechanisms it explains are covered or queued, each once. Cache busting is
  `fluid-resource-uris` in `knowledge/hints/fluid.json`, which states that the
  publisher applies it and that every resource URI carries one since 14. Cache
  invalidation is `todo/open/2026-08-02-211403` on
  [`D-KNW-027`](../knowledge/knw-027-which-caches-a-change-invalidates-is-a-gap-this-server-owns.md).
  The rendered preview is `todo/open/2026-08-02-200948` on
  [`D-KNW-017`](../knowledge/knw-017-a-verification-question-is-routed-to-the-layer-that-verifies-it.md).
- The checklist had nothing of this kind. Five items — confirm the branch,
  inspect nearby code, keep the patch focused, cover it, run the checks — and
  the `bugfix` block adds reproducing and checking older branches. All of them
  are about the patch, and none says what the change is for.
- The brief is what a building or fixing task passes through. `skills/base.md`
  orders it as step 3 of every task, the reporting session called it, and it is
  composed per call rather than paid for by every caller in every project.
- One sentence the feedback offers is not true of the code. "Nobody offers an
  option to omit the processing checksum" is its argument against optionality,
  and `useCacheBusting` is an argument of `Uri\ResourceViewHelper` on
  `.checkouts/14.3` and `.checkouts/main`, line 62, defaulting to true.
  `fluid-resource-uris` states the same. So the premise is written without a
  claim about what may be switched off.

## Decided

- One statement rather than three, and it names an outcome rather than a
  mechanism: cache busting, cache invalidation and rendering a preview to verify
  all follow from it, and each has a place of its own already.
- First in the checklist, before "confirm the branch". It is what decides which
  of the items under it matter, and an item somewhere in the middle is read
  after the assessment it was supposed to change.
- On the base checklist rather than in the `bugfix` block the feedback asked
  for. A feature delivers the old version just as a bug does, and stating it per
  change type is the same sentence in two places.
- Not in `skills/base.md` and not in the `instructions`.
  [`D-FBK-024`](../feedback/fbk-024-a-feedback-about-the-callers-conduct-toward-its-user-names-no-surface.md)
  prices both — 2048 characters on one client's evidence
  ([`D-ANS-004`](../answers/ans-004-the-instruction-budget-is-2048-characters-on-one-clients-evidence.md)),
  and a base skill
  [`D-SKL-001`](../task-skills/skl-001-the-order-a-task-starts-in-is-one-file.md)
  watches the length of — and neither cost is owed for a statement the tool at
  step 3 can carry.
- Two of the feedback's three concrete clauses are not carried, and the feedback
  is archived with that said rather than trimmed to them. That an editor does
  not distinguish a FAL image from a package resource is what
  `fluid-resource-uris` already states as code, since `PublicResourceInterface`
  hands both to one generation; that two APIs for one outcome make their
  inconsistency the bug is a claim about API design rather than about TYPO3, and
  it is the kind of second statement this entry exists to refuse.
- It is written as a statement about TYPO3 and not as a rule about how a session
  conducts itself, which is the boundary `D-FBK-024` draws. What is claimed is
  what the product owes an editor and a visitor; when to stop and ask the user
  is the question that entry leaves open.

## Assumed

- That the premise carries the three mechanisms rather than needing them beside
  it. The reporting session had the facts and drew the wrong conclusion, which
  is what says the missing part was the premise; nothing has measured a session
  that has the premise and no mechanism.
- That a caller reads the checklist. The reporting session called this tool and
  recorded what it got, but no transcript shows which part of the answer it
  acted on.
- That the premise reads as true outside the core. It is the same product for a
  sitepackage, and the sentence names no core artifact, so the outside-core
  filtering keeps it — but every feedback behind it came from core work or from
  one site project.

## Wrong if

- A session reads the premise and still assesses a report by whether the API was
  used correctly. The words were then not what was missing, and what is left to
  suspect is the surface: a checklist item is read after the assessment has
  formed.
- `typo3_task_guide` gains the shape
  [`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
  demands and the premise falls out with the change checklist. It holds for a
  review more than for a patch, so it would need a place that survives that
  split.
- A session agrees with the premise and ships a stale asset anyway. Then the
  outcome is stated where the mechanism was needed, and the three cards are the
  answer rather than its corollaries.
