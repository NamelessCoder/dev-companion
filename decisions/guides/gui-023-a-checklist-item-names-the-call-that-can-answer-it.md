---
id: D-GUI-023
title: A checklist item names the call that can answer it
date: 2026-08-27
status: open
coveredBy:
  - HintsTest::theBugfixChecklistNamesTheCallThatSettlesTheReleaseBranches
---

# D-GUI-023 — A checklist item names the call that can answer it

**Where a brief asks for something the caller's checkout cannot supply and a
tool here can, the item names the call instead of the reading.**

The bugfix checklist asked whether the defect also affects maintained older
release branches, and a core checkout regularly holds one branch.

## Evidence

- `feedback/2026-08-27-145507` reports doing exactly that substitution by hand:
  "I could not act on this from the checkout — I only have `main` — and I
  resolved the branch question from typo3_commit_message_guide instead, which
  told me that a BUGFIX goes to main and 14.3 and that naming 13.4 claims a
  severity I would have to justify. That was the better answer, and the
  checklist item pointed at work I had no way to do."
- The tool does answer it. Re-run on 2026-08-27 for `changeType="BUGFIX"` and
  `workflow="core"`, the Releases warning names the lines that can take a patch
  at all and says an older one is named only where the severity earns it.
- The item as written names no source. Every other item of that checklist either
  states the rule or names the call, which is what made this one read as a
  reading to go and make.

## Decided

- The item names `typo3_commit_message_guide` and keeps the half the tool cannot
  answer: whether the defect is on those branches is the caller's reading, and
  the item says a one-branch checkout cannot make it. Dropping that half would
  turn a question the caller owes into one the tool has answered.
- Not a condition on the item. The session's other suggestion is that items say
  what makes them apply, which is a change to every item rather than to this
  one; `T-260827-5c50` carries that and this entry does not pre-empt it.

## Assumed

- One session, and its account of what it did instead. That the substitution was
  the better answer is its judgement, and the tool's own text is what agrees
  with it here.
- That a core checkout usually holds one branch. It is what this repository's
  own `.checkouts/` is built as, one worktree per covered version, and what the
  reporting session had.

## Wrong if

- A session reports the item as a call it did not need, having the branches in
  front of it. The reading would then be cheaper than the call for anyone
  working across worktrees.
- The tool's answer is read as settling which branches carry the defect. That is
  the half the item keeps, and a session acting on the Releases line alone would
  say the sentence did not hold it apart.
