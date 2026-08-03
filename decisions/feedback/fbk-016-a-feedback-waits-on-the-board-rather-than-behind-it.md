---
id: D-FBK-016
date: 2026-08-02
status: open
---

# D-FBK-016 — A feedback waits on the board rather than behind it

**Every open feedback has a todo of its own, written by `bin/cli todo:sync`, and
what asks for is the judgement rather than the fix.**

The pile was reachable only through a sighting that ran when the queue was
empty. A card per feedback puts it in the one place a session already looks, and
the priority does what the group boundary did.

## Evidence

- 67 open feedback against an empty queue on 2026-08-02, every one of them
  unjudged. The sighting was therefore due in every session, and the pile was
  visible only through a command written for it —
  [`D-FBK-012`](fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md)
  and the 56-against-38 measurement it carries.
- The relation was already there. `Todo::serves()` reads the queue, what is in
  hand and what waits, and `OpenFeedback` marks a feedback judged when any of
  the three names it — so a card in `todo/open/` marking a feedback as taken on
  needed no new state, only a command that writes one.
- Absence of a priority was decided the same day and for this.
  [`D-FBK-015`](fbk-015-a-priority-is-a-class-and-the-stamp-is-the-rest.md)
  gave the order a fourth thing to say — nobody has judged this — which is what
  a feedback card needs and what nothing else does.

## Decided

- One card per open feedback, named after the feedback so the pair is visible
  and the card's age is when the report arrived. `bin/cli todo:sync` writes what
  is missing and is idempotent, because what already has one is read from
  `Todo::serves()` rather than tracked.
- **The card points and does not copy.** `**Serves:**` names the file and the
  heading is the feedback's own, so a listing says which one it is; nothing else
  of the report is in the card. Two copies of a report drift, and the one in
  `todo/` would be the one nobody corrects.
- The step is the same on every card because it is the same step: judge it, and
  `documentation/feedback/judging.md` is where that is written. What the six
  answers are does not belong in 67 files.
- More than one card for one feedback is legitimate. A single report can need
  more than one change, and the relation was always many-to-one.
- `bin/cli feedback:next` and the sighting that ran it are deleted. Handing over
  the oldest unjudged feedback is what `bin/cli todo:next` now does, by the
  priority rather than by a group, and a second command for it is a second
  answer to one question. `bin/cli feedback:list` stays: reading the pile is a
  different question from working it.

## Assumed

- That the backlog sighting still comes round. It is reached when the queue is
  empty, which now also requires that no feedback is unjudged — rarer, and
  correct as long as judging really is the more urgent of the two.
- That 67 unjudged cards do not drown the board. They sort below everything
  judged, so a session sees them only when nothing decided is left, and
  `bin/cli todo:list` prints one line each.
- That the sync is run. Nothing calls it on a schedule, and a feedback recorded
  after the last run has no card until somebody does.

## Wrong if

- The board stops being read because it is mostly unjudged cards, and sessions
  go back to `bin/cli todo:list | head`. That is the sighting's failure arriving
  from the other side: too much at once, seen all at once.
- The backlog sighting stops coming round at all, because the queue is never
  empty. Then judging a feedback and naming a backlog entry are competing for
  the same slot and the second needs a clock of its own.
- Cards accumulate for feedback that were closed elsewhere, or feedback
  accumulate with no card. Either means the sync is not being run, and the
  relation is then a claim the board makes and does not keep.

## Covered by

- `TodoTest::everyOpenFeedbackIsOnTheBoard`
- `TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead`

## Since then

The third assumption failed the same day it was written. Commit `8ef3bba`
brought 20 feedback in and wrote no card for any of them, and the board said so
only when the next session ran `bin/cli todo:check`. Nothing else about the
entry moved: the cards were written by the sync as designed, and the count had
not drowned anything. What was missing was the caller.
[`D-FBK-022`](fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md)
is that caller — a pre-commit hook that runs the sync where the commit touches
`feedback/` — so the assumption is now held rather than made.
