---
id: D-FBK-010
title: '`main` carries the state and the branch carries the work'
date: 2026-08-01
status: open
coveredBy:
  - TodoTest::aClaimCarriesTheBranchItWasGiven
  - TodoTest::aClaimIsOneMoveThatGoesBothWays
---

# D-FBK-010 — `main` carries the state and the branch carries the work

**Several sessions work several todos by claiming them on `main` and working
each on its own branch in its own worktree.**

`todo/` was already the shape this needs. One todo is one file, decided in
`D-FBK-008` so that two sessions could add, move or drop work without writing
the same file — and the case it was decided for was two sessions doing that one
after the other. The same property carries them doing it at once: each session
touches its own file in `todo/` and no other, so the claims never conflict.

## Evidence

- The queue on the day this was written — 29 items, nearly all in the shape
  "read `D-XXX` against what the code does now", each naming a different entry
  and a different part of the checkout. That is work with no overlap and no
  order between the items, which is the case parallel sessions are worth
  anything for. What made it impossible was one command: `todo:next` handed all
  of them the same first item.

## Decided

- A fifth place, `todo/progress/`, beside the queue, what recurs, what waits and
  what is kept for reading. `bin/cli todo:claim <n>` moves the front of the
  queue into it and `bin/cli todo:release` moves one back. The second is not an
  extra: a session ends where it ends, and a state that can only be entered
  fills up with claims nobody is working and nobody else is offered.
- That `bin/cli todo:next` reads the branch the checkout is on and hands over
  that branch's claim. `AGENTS.md` says where a session starts before it says
  anything else, and that sentence has to keep being true in a worktree —
  otherwise the one thing every session does is the one thing a parallel session
  must not.
- That this repository owns the moving of files and the naming of branches, and
  names the rest. The worktree, the merge and what a question that arrives
  mid-work leaves behind are on one page,
  [documentation/records/working-todos-in-parallel.rst](../../documentation/records/working-todos-in-parallel.rst),
  handed over with every claim — the same line `todo:next` draws when it names a
  `Run:` command it does not own instead of running it.
- That a question a session cannot settle is recorded rather than asked. One
  session working alone asks and waits; one of several would block a worktree on
  a person answering three others. It writes the question into `**Waiting on:**`
  on its claim, commits what it has, and ends — and only the claim comes back to
  `main`, so `main` says what is open and where the work is without carrying the
  half-finished change.

## Assumed

- That a worktree costs less than the collisions it prevents. It is a
  `composer install` per claim, and `vendor/` cannot be shared: `Paths::root()`
  is the directory above `src/`, Composer resolves `src/` from where the
  autoloader physically sits, and a symlinked `vendor/` therefore points every
  path in this repository back at the main checkout. That was found by running
  it, not by reading it — `bin/cli todo:next` in the worktree answered with the
  main checkout's queue and nothing about the answer looked wrong.

## Wrong if

- `progress/` grows a claim nobody has released in a fortnight, which would mean
  the state is cheap to enter and expensive to leave — the same failure
  `waiting/` is watched for, and there is no recurring todo asking after this
  one. Or a merge conflicts inside `todo/`, which would mean the one file per
  todo does not hold what it is being asked to hold here. Or the claims keep
  being taken two at a time on work that turns out to share a file, in which
  case the overlap warning is reading the wrong signal: it counts entries a todo
  serves, and most of this queue serves a directory.

## Since then

The boundary the fourth **Decided** drew has moved, and only that one.
`bin/cli todo:claim` now commits the claims onto `main`, cuts a worktree per
claim, installs in each and starts a session in each — it carries the
arrangement out instead of naming it. What held for as long as the rest was done
by somebody reading a page stopped holding when it was done by a session: three
steps that must happen in one order are one step, and the run that got the order
wrong paid for it hours later, in a worktree, as a refusal nobody there could
place. The page is still where the merge and the mid-work question live, because
neither is a step this command could take.

## Since then

The launch has moved across too, on 2026-08-02 and for the same argument one
step further out. The command printed the message and left the starting to
whoever read the output, and a step left over for somebody to carry out by
reading is the one that breaks: every session of that day was started in the
directory that was already open, and three worktrees stood untouched while the
sessions read a queue belonging to somebody else. What the client is called is
still not this repository's business, so `.session-command` is the machine's and
gitignored, and the three things a person gets wrong are the command's — the
working directory, the message, and a session id apiece.

## Since then

The assumption was measured on 2026-08-13, over three rounds of eight claims: 24
branches in one night, each brought home as its session reported. Five collided
on a decision id and one pair collided in a file — the pair `todo:claim` had
named in its overlap warning before the sessions started, which is what the
second and third readings were added for. All of them were repaired in the
worktree they happened in, and none cost a revert off `main`. What homing per
branch bought is in the same measurement: a round's fastest session finished in
5 minutes and its slowest in 25, and the slow one carried the file conflict, so
it rebased onto a `main` that had already absorbed the other seven.
