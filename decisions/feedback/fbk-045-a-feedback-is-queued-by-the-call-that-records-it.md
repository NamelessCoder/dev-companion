---
id: D-FBK-045
date: 2026-08-14
status: open
---

# D-FBK-045 — A feedback is queued by the call that records it

**`typo3_feedback_record` writes the card that asks for the judgement, in the
same call that stores the report.**

The card used to be written by a command run from a pre-commit hook, so the
board was right on the checkout that committed and nowhere else.

## Evidence

- 13 feedback recorded on 2026-08-13 stood in the working tree with no card.
  `TodoTest::everyOpenFeedbackIsOnTheBoard` named all 13, and the repair was
  `bin/cli todo:sync` run by hand.
- The hook fires on a commit that touches `feedback/` in this checkout. A
  session that records a feedback and goes on working has its report on disk and
  nothing on the board until somebody commits here, which may be another session
  on another day.
- [`D-FBK-022`](fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md)
  rejected exactly this, on the grounds that `Upkeep/` already reads `Feedback/`
  and a card written from there would make that a cycle. The cycle is real; what
  it argues about is where the code sits.

## Decided

- `Channel::record()` writes the card after the feedback, so the pair is never
  apart and no step has to be known about.
- What a card is moved to `Feedback\Card` — the step, the priority, the name it
  takes from the feedback. `Upkeep\Todo` reads it from there, so the arrow
  between the two groups still points one way.
- `bin/cli todo:sync` and the pre-commit hook that ran it are deleted. A repair
  kept for a case the recording no longer produces is a second way for a card to
  come about, and this repository writes cards one way.
- `.githooks/` goes with the hook, and so does the `composer install` step that
  pointed git at it. It held one hook and that hook was the sync.
- What is left of the relation is the report. `bin/cli todo:check` names each
  open feedback no todo answers for, and
  `TodoTest::everyOpenFeedbackIsOnTheBoard` fails on it; the repair is a card
  written into `todo/open/` by hand, which is what a feedback that got here by
  hand costs.
- The card is written into the checkout the feedback was stored in rather than
  `Paths::root()`, which is the same directory in an installation and is not one
  in a test writing into a store of its own — `R-COD-003`.
- The tool answers with the card as `todo`, because the caller is told where its
  report is waiting rather than only where it was written.
- Rejected: writing the card from `Upkeep`, which is what `D-FBK-022` measured
  the cycle against.

## Assumed

- That every feedback belongs on the board. That is what the sync did for every
  open one; a report nobody wants judged has no state that says so.
- That a session recording and closing a feedback in one breath deletes the card
  with it. The judging ladder already asks for that deletion, and it is now
  possible one commit earlier than before.
- That the store's parent is a checkout of this repository. Pointed elsewhere
  with `TYPO3_DEV_COMPANION_FEEDBACK_DIR`, the recording creates a `todo/open/`
  beside whatever directory was named.
- That a queue that cannot be written is a checkout that could not have stored
  the feedback either. The write throws where it fails, which fails the
  recording after the report is on disk.
- That nothing else was writing feedback files. A script or an editor that puts
  one below `feedback/` now leaves the board short, and what used to repair that
  is gone.

## Wrong if

- A recording is reported as failed while the feedback is there, because the
  card could not be written. Then the card is what gets swallowed, not the
  report.
- Cards arrive that a session did not mean to bring: the feedback was to be
  trimmed or closed in the same change, and deleting the card becomes routine
  enough that a real one goes with it.
- `bin/cli todo:check` starts reporting feedback with no card again, often
  enough that writing one by hand is a step people know by heart. Then the
  repair was carrying something, and what it was carrying is what to find.

## Covered by

- `FeedbackTest::aRecordedFeedbackArrivesWithTheCardThatAsksForItsJudgement`
- `FeedbackTest::theToolReportsTheCardTheFeedbackWasQueuedAs`
- `TodoTest::everyOpenFeedbackIsOnTheBoard`
