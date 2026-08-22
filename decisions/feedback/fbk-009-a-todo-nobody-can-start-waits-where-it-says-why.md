---
id: D-FBK-009
title: A todo nobody can start waits where it says why
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

- `Wait for the producer D-KNW-004 needs`, at the end of a queue of 34 on the
  day this was written. Its own paragraph said it waits on something outside
  this repository entirely, and its position said it is lower priority than 33
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
  is a file nobody opens again. The paragraph stays the one todo's
  (`D-FBK-003`); `bin/cli todo:list` is where the questions are readable.

## Assumed

- That the two cases can be told apart when the commit is written — a question
  that is merely unasked goes last, one that cannot be answered here waits.
  Nothing checks it, and the cheap mistake is the wrong one: a todo parked in
  `waiting/` is out of every session's way, which is exactly what a session that
  does not want to work it would choose.

## Wrong if

- ~~`waiting/` grows past two or three, which would mean it has become where
  todos go to be forgotten rather than where questions are kept.~~ Fired on the
  number on 2026-08-22, at six, and not on what the number stands for — see the
  reading below.
- A todo sits there with its question answered in the conversation and nobody
  moved it back, which would mean the seven-day todo is being run and not acted
  on.
- The count in `next` turns out to be read as work and starts being asked about;
  then it belongs only in `todo list`.

## Since then

The first **Wrong if** fired on the number and is struck. `waiting/` holds six
on 2026-08-22, dated from 2026-08-05 to 2026-08-19, where this entry named two
or three as the point at which the directory has become where todos are
forgotten. What the reading finds is that it has not.

Two of the six were put to the maintainer and answered — the gate on 2026-08-19
and the instruction budget on 2026-08-21 — and both answers said to wait, so
each carries a different question from the one it arrived with rather than a
place back in the queue. That is a state this entry did not anticipate: a todo
can be answered and still be waiting, and the seven-day reading is what asked
both.

One waits on nothing of its own. `2026-08-19-090231` says so in as many words:
what it needs is the skill its sibling in the same directory is blocked on. A
chain inside `waiting/` is the shape closest to the forgetting this **Wrong if**
describes, because no answer addressed to anybody releases it — and it is one
todo of six rather than the directory.

So the number is a proxy for a corpus of four questions rather than six todos,
and the two remaining clauses are what to read next time: the seven-day todo ran
on 2026-08-19, and nothing has asked about the count in `next`.
