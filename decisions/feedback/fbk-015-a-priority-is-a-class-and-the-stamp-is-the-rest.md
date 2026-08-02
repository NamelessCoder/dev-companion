---
id: D-FBK-015
date: 2026-08-02
status: open
---

# D-FBK-015 — A priority is a class, and the stamp is the rest

**A queued todo carries one of three words, the order below that is the date in
its name, and nothing is renamed to move.**

The number in the name was a rank, which is a thing only one session at a time
can hand out. Three words and an arrival time say what it said, and two sessions
queueing work at once cannot pick the same one.

## Evidence

- `bin/cli todo:check` carried a collision report for two files claiming one
  number, and
  [`working-todos-in-parallel.md`](../../documentation/feedback/working-todos-in-parallel.md)
  named renumbering as the fix in the paragraph about bringing branches home.
  Both existed because both sessions read the same last number and both took it
  — a rank has to be unique, and nothing could make it so across two branches.
- The tens. [`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md)
  chose them so a todo could be put between two others, and wrote as its
  **Wrong if** that a commit renaming more than a handful of files to move one
  would show them too tight. Nothing ever reordered the queue under that shape,
  so the mechanism was never used and its cost was paid on every insert.
- The page of what is deliberately not queued, on 2026-08-02. Its four catalog
  items say of themselves that nothing blocks them and that they serve no open
  feedback, "which is why it is below everything that does" — a priority,
  written as prose in a file outside the queue because the queue had no way to
  hold it.

## Decided

- Three words, `high`, `normal` and `low`, declared in `Todo::PRIORITIES` and
  nowhere else. A class rather than a rank: two todos may share one, and there
  is no between to put a third in.
- **Absence is the fourth thing it can say.** A todo with no `**Priority:**` is
  one nobody has judged, and it sorts below `low`. That is what a sighting used
  to do with what had only just arrived, and it is what lets a card be written
  for a feedback before anybody has decided what the feedback is worth — so
  nothing needs a default, and a default would have hidden the state.
- The stamp in the name is the order below the word, in the shape a feedback is
  already named in. `read()` sorts by name and PHP's sort holds equals in
  place, so the second half of the order costs nothing.
- A claim keeps its name. The number was a place in one order and had to be
  dropped where that order did not reach; the stamp is when the work arrived,
  which is as true in hand as in the queue, so a claim and a release are moves
  and nothing else.
- **`release()` no longer re-ranks.** A released todo used to be renumbered to
  the end, because the queue was one order and there was no other way to say
  "later". The two things that meant are now said separately and by a person:
  where it cannot be worked, `waiting/`; where it can and should not be next, a
  lower priority — a line somebody wrote and can be disagreed with, rather than
  a place a command moved it to.

## Assumed

- That three is enough. Two would not separate "next" from "not now", and five
  invites a rank by another name — but nothing has yet needed a fourth, because
  nothing has yet had more than a handful at one priority.
- That the ordering people want is a priority. The one case to hand is: tidying
  the store before 67 cards land in it is a preference and not a constraint —
  either order works — so `high` says it exactly. What no number of priorities
  could say is "not before that one", and nothing has needed it yet. A todo that
  truly cannot start is `waiting/`, and two steps that must run in order are
  usually one todo.
- That a stamp keeps meaning arrival. Nothing rewrites one today, and the two
  moves that could — a claim and a release — deliberately do not.

## Wrong if

- Everything queued ends up `high`, or nothing carries a priority at all. The
  first is a rank returning through the vocabulary, the second means the field
  is ceremony and the order is really the age. `bin/cli todo:list` prints the
  word on every line, so either is visible from one command.
- A dependency is written as a priority often enough that the word stops
  meaning importance. The assumption above says this has already happened once.
- Two todos at one priority and one age turn out to need separating, which
  would mean the stamp is too coarse or the order was a rank after all.

## Covered by

- `TodoTest::theQueueIsReadByPriorityAndThenByAge`
- `TodoTest::aClaimIsOneMoveThatGoesBothWays`

## Since then

The fourth thing a priority could say is gone, on the day it was written. Every
todo in a stage carries one of the three words, a recurring one carries none,
and a card written for a feedback starts at `low` — which is where absence put
it, said in a word.

What it bought was the check. While absence meant *nobody has judged this*, a
priority somebody forgot and one deliberately left off were the same file, and
nothing could name either: an optional field cannot be required, so `todo:check`
had to stay silent about the one case it most wanted to report. Making it
required cost nothing that was being used, because what a card is for is
readable anyway — a judging card is the one that serves a `feedback/` file.

The first **Wrong if** above still stands and is now the whole of the watch: if
everything on the board is `high`, the rank has come back through the
vocabulary. The second half of it — *nothing carries a priority at all* — is no
longer a way this can go wrong, because a file like that is reported.
