---
id: D-DOC-036
date: 2026-08-18
status: open
---

# D-DOC-036 — A todo serves a decision by its id

**A `Serves:` line may name one decision by its id, and `decisions/` goes on
being what a todo sorting the pile names.**

## Evidence

- The two lists never agreed. `Todo::unreadable()` took a requirement, a
  scenario, a feedback and a directory from the day it was written, 2026-07-31;
  `documentation/records/working-a-todo.rst` was written the next day naming a
  requirement, a decision, a feedback and a directory, and neither list moved
  afterwards. A `**Serves:** D-DOC-035` written on 2026-08-18 failed
  `bin/cli todo:check` while the page said it was one of the four.
- `bin/cli unresolved:list` reports the open decisions as a count and asks only
  whether some todo serves `decisions/`. An entry whose **Wrong if** somebody is
  going back to was therefore queued under the same word as the whole pile.
- [`D-FBK-017`](../feedback/fbk-017-a-judgement-turns-a-feedback-into-work-and-the-work-closes-it.md)
  weighed moving the todos derived from a feedback onto the decision entry
  instead, and refused it for what `closed` would then mean to an agent — not
  for a decision being something a todo cannot name.

## Decided

- `Todo::unreadable()` reads the `D-XXX-000` shape against `Decisions::all()`,
  beside the requirement branch it already had.
- Both lists name the same five, and `todo/readme.md` says which of the two
  forms a decision takes: the id where the step is that entry's **Wrong if**
  gone back to, `decisions/` where it is the pile that is being sorted.
- Against correcting the page down to four. The sentence under the list — a todo
  serving a decision is usually that entry's **Wrong if** compressed into a
  sentence — describes a reading that has no other field to name its subject in.

## Assumed

- That the recommendation the todo carried is what was wanted. The question is
  one nothing here answers, and it was not asked: the todo was queued with both
  options and a recommendation and then handed out to be worked, which is read
  as the answer rather than as a blank.
- That an id on a `Serves:` line means the entry itself and not the pile, so
  nothing counts such a todo as the sorting `unresolved:list` asks about.

## Wrong if

- No todo ever names one. Then the branch is a shape nobody writes, and the page
  rather than the code was the thing that should have moved.
- `bin/cli unresolved:list` comes to want which open decisions are queued. It
  asks about `decisions/` alone, so an id it cannot see would make an entry
  somebody is working look like one nobody has been back to.

## Covered by

- `TodoTest::whatATodoServesIsCheckedAgainstThePlaceThatOwnsIt`
