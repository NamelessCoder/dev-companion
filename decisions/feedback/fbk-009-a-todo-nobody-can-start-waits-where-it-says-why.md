---
id: D-FBK-009
date: 2026-08-01
status: open
---

# D-FBK-009 — A todo nobody can start waits where it says why

**A todo blocked on an answer this repository cannot produce leaves the queue
for `todo/waiting/` and carries the question it waits on.**

Until now it went to the end of the queue. `D-FBK-007` decided that, and for a
todo whose question is merely unasked it is still right: last is also a timer,
and the todo comes round again as the queue drains. What it gets wrong is the
todo that no answer here can unblock, which then sits at the bottom of the order
looking like the least important thing in the repository.

## Evidence

- `Wait for the producer D-KNW-004 needs`, at the end of a queue of 34 on the day
  this was written. Its own paragraph said it waits on something outside this
  repository entirely, and its position said it is lower priority than 33
  pieces of work — two different claims, one of them made by the file system.
  Every session that reached it would have re-read the same paragraph to learn
  it cannot be started.

## Decided

- A fourth place beside the queue, what recurs and what is kept for reading. A
  todo there has no number, because it has no place in an order, and carries
  `**Waiting on:**` — the question in the words it was asked in, held by
  `bin/cli todo:check`. The answer is what numbers it back into the queue.
- That the way back is a recurring todo rather than a habit. Every seven days
  `bin/cli todo:waiting` prints what is blocked with its question and exits
  nonzero, which is what makes that todo due and puts the questions in front of
  a session before the queue. Without it the state is a directory nobody has a
  reason to open, and the end of the queue — for all it says wrong — at least
  came round again.
- That `bin/cli todo:next` names how many are waiting and nothing more, as one
  field of the line that already says how many are behind this todo. A blocked
  todo is addressed to whoever can answer it, and a state nothing ever mentions
  is a file nobody opens again. The paragraph stays the one todo's (`D-FBK-003`);
  `bin/cli todo:list` is where the questions are readable.

## Assumed

- That the two cases can be told apart when the commit is written — a question
  that is merely unasked goes last, one that cannot be answered here waits.
  Nothing checks it, and the cheap mistake is the wrong one: a todo parked in
  `waiting/` is out of every session's way, which is exactly what a session
  that does not want to work it would choose.

## Wrong if

- `waiting/` grows past two or three, which would mean it has become where
  todos go to be forgotten rather than where questions are kept. Or a todo sits
  there with its question answered in the conversation and nobody moved it
  back, which would mean the seven-day todo is being run and not acted on. Or
  the count in `next` turns out to be read as work and starts being asked
  about; then it belongs only in `todo list`.
