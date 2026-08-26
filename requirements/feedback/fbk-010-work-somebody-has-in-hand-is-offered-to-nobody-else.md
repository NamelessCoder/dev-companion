---
id: R-FBK-010
title: 'Work somebody has in hand is offered to nobody else'
status: held
heldBy:
  - TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt
  - TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim
  - TodoTest::whatIsInHandIsTheTodoAWorktreeStandsOn
---

# R-FBK-010 — Work somebody has in hand is offered to nobody else

**A todo a session has taken on is one the command that hands out work does not
hand out.**

The queue is an order, not an assignment. `bin/cli todo:next` reads the same
first item for everybody who asks, which is exactly right while one session
works at a time. It is wrong the moment two do: both are handed the same todo,
and the second finds out by writing a change the first has already written.

So taking one on is cutting a worktree on the branch the todo derives, and the
command that hands out work passes over every todo one stands on — `D-DOC-060`.
The todo does not move. It was a move into `todo/progress/` until 2026-08-27,
which said a third time what the branch and the worktree already said, and said
it in the one copy that outlives them.

What it took on stays taken on. A requirement whose todo dropped off the list of
what is answered for would go back onto `bin/cli unresolved:list` while somebody
is working on it, and the next session would queue it a second time — the same
trap `waiting/` was already kept out of.

The claim carries what tells it from one nobody came back to: the branch the
work is on, so a half-finished diff can be found, and the day it was taken, so a
state that locks everybody else out of one todo can be read as stale.

`bin/cli todo:next` stays where every session starts, worktree or not. A session
told its file name by whoever set it up would be the one session here that
begins differently, and the failure that invites is silent: handed the front of
the queue instead of its own claim, it reads a real todo, starts real work, and
is the second person doing it.

## From

2026-08-01. Two sessions working at once had no way to get different work, and
the whole of `todo/` was built around one session at a time.

## Held by

- `TodoTest::whatIsInHandIsTheTodoAWorktreeStandsOn` for what says a todo is
  taken.
- `TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim` for what
  `bin/cli todo:next` answers in a worktree.
- `TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt` for the way
  out that is not a deletion.
