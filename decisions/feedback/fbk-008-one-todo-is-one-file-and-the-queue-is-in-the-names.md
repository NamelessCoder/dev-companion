---
id: D-FBK-008
title: One todo is one file, and the queue is in the names
date: 2026-08-01
status: open
---

# D-FBK-008 — One todo is one file, and the queue is in the names

**Every todo is a file below `todo/`, and a queued one is numbered by its place
in the order.**

`todo.md` was the last shared log in a repository that had solved the same
problem twice. `requirements/` became a directory after five ids had been handed
out twice in one document; `decisions/` was written as one from the start. The
todos stayed a file, and the cost fell where there is least room for it: at the
end of a run, finishing a todo meant loading 30 kB to delete a paragraph.

## Evidence

- The file on the day it was split — 32 kB, 34 queued sections, three recurring,
  two that were neither. Every session that added, moved or finished work wrote
  it, so two sessions could not do so at once, and a state a directory carries
  as a place could only be prose. `D-FBK-002` had already declared what each
  section is, which is why the split was a move rather than a rewrite: the head
  survived intact.

## Decided

- One file per todo. `todo/NNN-<title>.md` is the queue and the number is the
  place in it, `todo/recurring/` is what comes round and is never deleted,
  `todo/reference/` is what a session would otherwise rediscover and mistake for
  work. The head of labelled lines stays exactly as it was, minus
  `**Not an item.**`, which the directory now says. Numbers run in tens so
  something can be put between two of them.
- Against a `todo/order.md` listing the files in order, which is the shape the
  proposal came in. It would have kept one file every session writes, and it is
  a hand-maintained copy of a directory — the opposite of what the group
  listings in `requirements/` and `decisions/` do, which is to be generated from
  the files. Against a position field in the head for the same reason it was not
  a table: the order would then be readable only after something has parsed 34
  files, and `ls` would say nothing.
- Against generating a listing into `todo/readme.md`. It would be correct and it
  would be written by every commit that finishes a todo, which is the shared
  file coming back through the door the split just closed. `bin/cli todo:list`
  is the overview, and the directory listing is the order.

## Assumed

- That renaming is what moving a todo costs, and that it is cheaper than editing
  a shared file. A move is one `git mv`; the tens are what keep an insertion
  from being a renumbering. Nothing has yet reordered this queue under the new
  shape.
- That a session still reaches for `bin/cli todo:next` rather than the
  directory, which is now sorted, titled and readable without a command. That
  bet is `D-FBK-003`'s, and this makes the cheap wrong path cheaper.

## Wrong if

- A commit renames more than a handful of files to move one todo, which would
  mean the tens are too tight and the number is doing work a field should. Or
  sessions start opening `todo/` instead of running `next`, which is
  `D-FBK-003`'s **Wrong if** reached by a route this decision opened. Both are
  readable in the history: the first in `git log --stat`, the second in what a
  session says it read.

## Since then

The first half stands and the second is gone. One todo is still one file, which
is what this entry was written to settle and what made every shape after it an
edit rather than a rewrite. The queue is no longer in the names: the number was
a rank, and a rank is something only one session at a time can hand out, so two
sessions queueing work on two branches both read the same last number and both
took it. A priority in the head and the arrival date in the name say what the
number said, and neither has to be unique —
[`D-FBK-015`](fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md).

The **Wrong if** above was never met and that is the point of recording this
here rather than revoking the entry. Nothing ever reordered the queue, so the
tens were never proved too tight; what failed was the assumption underneath
them, that the order is a rank at all.
