---
id: D-FBK-017
date: 2026-08-02
status: open
---

# D-FBK-017 — A judgement turns a feedback into work, and the work closes it

**A feedback is archived when the change it asked for has landed, not when
somebody has decided about it.**

Between the two sits a third state, which is where most feedback are: judged,
with the work queued and the report still open. Archiving at the judgement would
tell an agent its report was dealt with while the thing it reported is still
there.

## Evidence

- The two states were about to become one. The board was built on 2026-08-02
  with a card per feedback
  ([`D-FBK-016`](fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md)),
  and the process written around it read: judge, derive todos, archive. Under
  that order every derived todo would serve an archived feedback, which
  `Todo::unreadable()` reports as a problem — so the alternative was not merely
  worse, it was already refused by a check written for a different reason.
- What archiving says outward. `typo3_feedback_list` returns an archived
  feedback as `status: closed` with the commit that closed it, and
  [`R-FBK-002`](../../requirements/feedback/fbk-002-a-feedback-that-was-worked-off-stays-answerable-for.md)
  records what a wrong answer there costs: a re-report of a request that had
  shipped, because the agent read a missing file as lost.

## Decided

- Three states, and the middle one is not a place: open with nothing serving it
  is unjudged, open with a todo serving it is judged, and the archive means the
  improvement landed. AGENTS.md already said the third; what is new is naming
  the first two apart, because the board made the difference visible.
- **The invariant.** A commit that judges a feedback either archives it or
  leaves at least one todo serving it. Anything else drops it back to unjudged,
  where the next `bin/cli todo:sync` writes a fresh card and the judgement is
  gone. "Nothing to do" is therefore the *close* answer rather than a special
  case: archive in the same commit.
- A feedback that cannot be judged moves its card to `todo/waiting/` with the
  question, where it goes on serving the feedback. The card is not deleted and
  the feedback is not closed — a question nobody can answer is a state this
  repository already has a place for.
- `bin/cli todo:check` reports open feedback that no todo answers for, and names
  `bin/cli todo:sync`. `TodoTest::everyOpenFeedbackIsOnTheBoard` held it
  already; it is said here as well because a check is what a session runs and
  the suite is what somebody else runs.
- Against archiving at the judgement, and the reason is not tidiness. It would
  have to weaken `Todo::unreadable()`, and it would move the derived todos onto
  the decision entry instead of the feedback — readable, but it makes `closed`
  mean two different things depending on what the judgement decided.

## Assumed

- That an agent reads `closed` as "the thing I reported is dealt with". Nothing
  has measured it; what has been measured is the opposite error, a missing file
  read as lost, which is the same reader making the same kind of inference.
- That the middle state does not become where feedback go to die. It has no
  clock on it: a feedback judged into a `low` todo is open for as long as that
  todo waits, and nothing says how long that is.

## Wrong if

- The archive stops filling while `feedback/` grows, because everything is
  judged into todos nobody works. Then the middle state is a second backlog
  wearing the word "judged", and what is missing is an age on it.
- Somebody archives a feedback to get `todo:check` quiet. The error it reports
  in that case is one todo per derived step, which is loud enough to invite it.
- An agent re-reports something already judged, because `open` looked to it like
  nothing had happened. That is the mirror of the evidence `R-FBK-002` was
  written from, and it would mean the middle state needs to be visible through
  `typo3_feedback_list` rather than only in `todo/`.

## Covered by

- `TodoTest::everyOpenFeedbackIsOnTheBoard`
- `TodoTest::everyTodoAnswersForSomethingThatCanStillBeRead`
