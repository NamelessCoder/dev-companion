---
id: D-KNW-087
title: A listed neighbour says what it prevents
date: 2026-08-18
status: open
coveredBy:
  - HintsTest::anUndeclaredContentAreaIsSaidToThrowAndTheEmptyOneToRenderNothing
---

# D-KNW-087 — A listed neighbour says what it prevents

**The six closing statements that list their neighbours say what each one
prevents, and `page-content-areas` is corrected first, because it denies the
consequence a pointer at it would name.**

A session read the pointers, took about half of them, and paid for two it
dropped: five review findings and an HTTP 500 on every page.

## Evidence

- `feedback/2026-08-17-211306`. A build of a v14 demo site over 22
  `typo3_hint_lookup` calls, tracing its own transcript rather than recalling
  it. It followed roughly half the neighbour ids it was offered.
  `project-build-and-scripts` was named twice and fetched after the work, and
  held the answer to five of a reviewer's ten findings; `page-content-areas` was
  named, skipped, and fetched three calls later to diagnose the HTTP 500 it
  would have prevented.
- The corpus today: 139 hints, of which 37 close by naming another hint's id,
  and 6 of those open with the bare formula — `sitepackage-initial-content`,
  `extension-repository-layout`, `frontend-page-rendering`, `installation-boot`,
  `site-sets`, `browser-tests`. Three of the six are ones the feedback names as
  skipped.
- Delivery and routing were not the failure. The session was handed the
  sentences and read them, which is what puts this at step 4 of the ladder
  rather than at step 2 or 3.
- The feedback's positive control is not a neighbour reference.
  `sitepackage-templates`' layout-collision warning, which it says made it act
  immediately, is a statement in that hint's own body about its own subject. So
  the contrast drawn is between a rule and a pointer, not between two pointers,
  and the mechanism proposed — that a consequence makes a pointer take — is
  untested by the evidence offered for it. `content-element-preview` closes with
  a pointer that does give a reason, and that one was skipped too.
- The consequence the feedback wants written is stated the other way round in
  the hint it points at. `page-content-areas` says a column without an explicit
  identifier "cannot be addressed by name, and the template then renders empty
  with no error", while the same hint prescribes
  `<f:render.contentArea contentArea="{content.main}" />` as the way to render
  one. `ContentAreaViewHelper::render()` throws `InvalidArgumentValueException`
  1770212183 for a `contentArea` argument that is not a `ContentArea`, in
  `.checkouts/14.3` and `.checkouts/main` alike — which is the 500 the session
  reported.

## Decided

- Step 4 of the ladder, queued rather than closed in this run. What a rewritten
  pointer states is a consequence about TYPO3, and
  `documentation/records/judging.rst` puts a lookup on the todo side of that
  line. Nothing in `src/`, in a declared schema or in a skill moves.
- The six formula statements are the scope. The 31 references that already carry
  a reason are left alone: nothing established here says a pointer with a reason
  fails, and `content-element-preview` is a case against rewriting them all.
- `page-content-areas` is corrected in the same work and before the pointer at
  it. A pointer naming a consequence the pointed-at hint denies is worse than
  the list it replaced, because the reader who follows it arrives at the
  contradiction.
- No requirement is written yet. What must hold from now on — whether a
  neighbour reference owes a consequence at all, or only where the neighbour
  guards a failure — is what the counter-case leaves open, and a requirement
  asserting it today would be one sentence ahead of its evidence.
- The card is raised to `normal`. The cost is measured and the same mechanism
  was reported twice, but by one session rather than by two, which is what keeps
  it off `high`.
- The scheduling half of the suggestion — that a neighbour mattering only at a
  later stage says so — is left where its own card already has it.
  `feedback/2026-08-17-211118` reports it against a skill's multi-id step, which
  is a different surface from a hint's closing statement, and that card is in
  hand elsewhere.

## Assumed

- That the formula is what discriminates. The six read as a table of contents
  and the other 31 read as prose giving a reason, and the reporting session
  skipped examples of both.
- That the session's 500 came from the ViewHelper rather than from the
  page-content processor above it. The exception code matches what the hint
  prescribes and nothing else was read.

## Wrong if

- The six are rewritten and a session reports skipping one anyway with the
  consequence in front of it. Then the wording is not the lever, where the offer
  sits is, and this is step 5 against `D-KNW-032` rather than step 4.
- The correction to `page-content-areas` holds only for an identifier the layout
  never declared, and a declared column that is empty renders quietly after all.
  The pointer would then warn about a trap on the path the reader is not on.
- A session reports the opposite cost: a pointer written as a warning followed
  into a hint the task did not need, crowding the limit. That is the second
  **Wrong if** of `D-KNW-032` arriving from this side.

## Since then

The second **Wrong if** is half true and does not falsify the entry. A declared
column holding no records does render quietly: `ContentArea::getRecords()`
returns nothing, the ViewHelper's loop produces the empty string, and no error
is raised anywhere. What throws is the other path —
`ContentAreaCollection::has()` does not know an identifier the layout never
declared, `StandardVariableProvider::getByPath()` returns null for it, and the
ViewHelper refuses anything that is not a `ContentArea`. That is the path the
reporting session was on, so the pointer warns about the trap the reader is
actually walking into rather than the one beside it. Both paths are now
statements of their own in `page-content-areas`, and what an undeclared column
costs carries a version binding the old sentence did not have: on 14 it is keyed
on an md5 of the layout identifier and the colPos with a deprecation, on 15 the
layout resolution refuses it outright. `site-set-labels-and-layouts` carried the
same denial in its own words and is corrected with it.
