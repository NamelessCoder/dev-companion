---
id: D-FBK-10
date: 2026-08-01
status: standing
---

# D-FBK-10 — `main` carries the state and the branch carries the work

**Several sessions work several todos by claiming them on `main` and working
each on its own branch in its own worktree.**

`todo/` was already the shape this needs. One todo is one file, decided in
`D-FBK-8` so that two sessions could add, move or drop work without writing the
same file — and the case it was decided for was two sessions doing that one after
the other. The same property carries them doing it at once: each session touches
its own file in `todo/` and no other, so the claims never conflict.

- **Evidence:** the queue on the day this was written — 29 items, nearly all in
  the shape "read `D-XXX` against what the code does now", each naming a
  different entry and a different part of the checkout. That is work with no
  overlap and no order between the items, which is the case parallel sessions are
  worth anything for. What made it impossible was one command: `todo:next` handed
  all of them the same first item.
- **Decided:** a fifth place, `todo/progress/`, beside the queue, what recurs,
  what waits and what is kept for reading. `bin/cli todo:claim <n>` moves the
  front of the queue into it and `bin/cli todo:release` moves one back. The
  second is not an extra: a session ends where it ends, and a state that can only
  be entered fills up with claims nobody is working and nobody else is offered.
- **Decided:** that `bin/cli todo:next` reads the branch the checkout is on and
  hands over that branch's claim. `AGENTS.md` says where a session starts before
  it says anything else, and that sentence has to keep being true in a worktree —
  otherwise the one thing every session does is the one thing a parallel session
  must not.
- **Decided:** that this repository owns the moving of files and the naming of
  branches, and names the rest. The worktree, the merge and what a question that
  arrives mid-work leaves behind are on one page,
  [documentation/feedback/working-todos-in-parallel.md](../../documentation/feedback/working-todos-in-parallel.md),
  handed over with every claim — the same line `todo:next` draws when it names a
  `Run:` command it does not own instead of running it.
- **Decided:** that a question a session cannot settle is recorded rather than
  asked. One session working alone asks and waits; one of several would block a
  worktree on a person answering three others. It writes the question into
  `**Waiting on:**` on its claim, commits what it has, and ends — and only the
  claim comes back to `main`, so `main` says what is open and where the work is
  without carrying the half-finished change.
- **Assumed:** that a worktree costs less than the collisions it prevents. It is
  a `composer install` per claim, and `vendor/` cannot be shared: `Paths::root()`
  is the directory above `src/`, Composer resolves `src/` from where the
  autoloader physically sits, and a symlinked `vendor/` therefore points every
  path in this repository back at the main checkout. That was found by running
  it, not by reading it — `bin/cli todo:next` in the worktree answered with the
  main checkout's queue and nothing about the answer looked wrong.
- **Wrong if:** `progress/` grows a claim nobody has released in a fortnight,
  which would mean the state is cheap to enter and expensive to leave — the same
  failure `waiting/` is watched for, and there is no recurring todo asking after
  this one. Or a merge conflicts inside `todo/`, which would mean the one file
  per todo does not hold what it is being asked to hold here. Or the claims keep
  being taken two at a time on work that turns out to share a file, in which case
  the overlap warning is reading the wrong signal: it counts entries a todo
  serves, and most of this queue serves a directory.
