---
id: D-DOC-063
title: 'A prose sweep reaches what nobody is holding'
date: 2026-08-27
status: open
coveredBy:
  - ProseFormatTest
---

# D-DOC-063 — A prose sweep reaches what nobody is holding

**`bin/cli prose:format` named no path rewraps what its own checkout has in
hand. In a worktree that is the files its branch changed, and in the checkout
`main` stands on it is the corpus minus what the standing claims changed.**

A rewrap of a file another branch is holding is a conflict whichever side lands
first, and the sweep is what put the whole corpus on both sides of that.

## Evidence

- Two claimed branches carried a corpus rewrap on 2026-08-27,
  `todo/T-260824-bd21` and `todo/T-260824-ea52`. Both stopped `todo:home` on a
  conflict with cards `main` had rewritten or deleted, and both resolved to what
  `main` said, after which one of the two rewrap commits reduced to empty and
  dropped out.
- What made it likely is that a card is rewrapped in bulk: the sweep of
  `d386831b` landed on 22 open cards at once, because every unjudged one carried
  the same boilerplate paragraph a character past the column.
- The other direction conflicts too, measured in a scratch repository the same
  day: a branch that deletes a card the checkout rewrapped is
  `CONFLICT (modify/delete)` on the rebase. A rule about worktrees alone would
  have left that half open.
- The sweep is not the part to give up. On 2026-08-04, 23 files had been
  reverted by session after session and stayed permanently behind the formatter,
  which is what running it over everything was for.

## Decided

- **Where the command stands decides what it sweeps.** `Todo::linked()` already
  tells a claim's checkout from the one it was cut from, and both halves fall
  out of it: the branch's own changed files, or the corpus minus every standing
  worktree's.
- Changed means against `main`, committed or not, so a decision file a session
  has written and not added is held too.
- A named path stays the caller's word and reaches whatever it matches. The
  narrowing is of the default, which is what a session runs without thinking
  about it.
- Rejected: a `prose:format` that only ever rewraps what the branch changed. It
  is the same command in the main checkout, where nothing is in hand and the
  answer would be nothing at all.
- Rejected: teaching `todo:home` to resolve the class. It aborts the rebase and
  reports, and with the source closed this shape is left to a session that
  really did rewrite a card another one deleted, where the right answer is not
  the same every time.

## Assumed

- That a claim is cut from `main` and rebased onto it, which is what a sweep
  compares a checkout against and what `todo:home` already assumes.
- That two claims changing one file is the overlap `todo:claim` reports before
  the worktrees exist, and not something a formatter has to arbitrate.
- That two git calls per standing worktree are cheap enough to spend on every
  sweep.

## Wrong if

- A rebase in `todo:home` conflicts on a file whose only difference is where the
  lines break.
- The corpus falls behind the formatter again, because a worktree left standing
  for weeks keeps its files out of every sweep.
- Somebody has to name a path to get a file rewrapped that no claim is holding.
