---
id: R-FBK-010
status: held
---

# R-FBK-010 — Work somebody has in hand is offered to nobody else

**A todo a session has taken on is out of the queue, and the command that hands
out work does not hand out that one.**

The queue is an order, not an assignment. `bin/cli todo:next` reads the same
first item for everybody who asks, which is exactly right while one session
works at a time. It is wrong the moment two do: both are handed the same todo,
and the second finds out by writing a change the first has already written.

So taking one on is a move — out of the queue and into `todo/progress/`, the way
finishing one is a deletion and blocking on one is a move to `waiting/`. Where a
file sits is what it is, and a claim that lived in a field of a file still in
the queue would be a claim only the checkout it was set in can see.

What it took on stays taken on. A requirement whose todo dropped off the list of
what is answered for would go back onto `bin/cli backlog:list` while somebody is
working on it, and the next session would queue it a second time — the same trap
`waiting/` was already kept out of.

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

- `TodoTest::whatIsInHandIsOfferedToNobodyElse` for the queue a claim leaves
- `TodoTest::aWorktreeStandingOnAClaimIsHandedThatClaim` for what
- `bin/cli todo:next` answers in a worktree, and
- `TodoTest::aClaimIsOneMoveThatGoesBothWays` for the way back out. That the
- Branch and the date are there at all is `bin/cli todo:check`.
