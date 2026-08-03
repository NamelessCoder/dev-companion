---
id: R-FBK-014
status: held
restsOn: [D-FBK-040]
---

# R-FBK-014 — A judgement takes the card it replaced with it

**A feedback that another todo serves has no card of its own left asking for the
judgement, and `bin/cli todo:check` reports the one that is.**

`bin/cli todo:sync` writes one card per open feedback and skips whichever a todo
already serves, so no feedback is ever given a second card. That bounds the
relation one way only. A judgement that folds a feedback onto another todo's
`**Serves:**` line arrives after the card was written, and nothing looks back at
it: the card stays in the queue carrying the step every card carries — judge this
feedback — for a judgement that has already been made.

Nothing about the two says they are a pair. A card is titled after the feedback
and a judged todo after its work, so a listing prints them ten lines apart with
no word in common, and the next session claims the card, reads the feedback and
spends itself arriving where the repository already was. That is what this costs
when it is missed: a whole session, on a question somebody answered.

The card is what goes, and it goes in the commit that folds the feedback. What
is left is the todo somebody wrote, which is the one that says what is actually
to be done. Only reporting is automated: the deletion belongs to the session that
made the judgement, and a command that removes a claimed card is how a judgement
gets lost rather than found.

Two cards over one feedback are not the failure. A judgement may legitimately
split a feedback across two work items, and what is wrong is an unjudged card
standing beside a judged one — which is why the signal is the step the card still
carries, verbatim, rather than the count.

This is `R-FBK-010` from the other side. A claim says a piece of work is somebody
else's, and a card says a judgement is nobody's yet; both are false the moment
the work behind them is done, and both are read by a session that has read
nothing else.

## From

2026-08-03. One claimed session was spent on `todo/progress/2026-08-02-145217`,
whose feedback four decisions had already cited and another todo already served.
`bin/cli todo:check` reported `47 files, 0 problems` while both stood.

## Held by

- `TodoTest::noJudgementLeavesBehindTheCardItReplaced` for the board as it
  stands, and
  `TodoTest::theCardAJudgementReplacedIsFoundByTheStepItStillCarries` for what
  is a pair and what is a split. That a session sees it is
  `bin/cli todo:check`, which reports what `Todo::folded()` returns.
