---
id: D-FBK-022
date: 2026-08-02
status: open
---

# D-FBK-022 — A feedback brings its card in the commit that brings it in

**A commit that touches `feedback/` runs `bin/cli todo:sync` in a tracked
pre-commit hook and stages the cards it wrote.**

The relation was held by two guards and neither of them runs when a commit is
made. A hook is where the repair belongs, because that is the moment the pair
comes apart.

## Evidence

- Commit `8ef3bba` brought 20 feedback in with no card. `bin/cli todo:check`
  reported them the next time somebody ran it, which was the following session.
- Both guards existed at the time. `bin/cli todo:check` is a command somebody
  has to type, and `TodoTest::everyOpenFeedbackIsOnTheBoard` runs in
  `composer ci`, which is what CI runs on a push rather than what a commit runs.
- [`D-FBK-016`](fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md)
  named this under **Assumed** — "That the sync is run. Nothing calls it on a
  schedule" — and under **Wrong if**. Something calls it now.
- The hook costs 3 ms on a commit that touches no feedback, because it reads the
  staged names before it reads anything else.

## Decided

- It repairs rather than refuses. `bin/cli todo:sync` is idempotent and writes
  exactly what the session would have to write anyway, so failing the commit
  would cost a second attempt and produce the same files.
- It runs only where the staged change touches `feedback/`, which is the only
  way the pair can come apart.
- `.githooks/` is tracked and `core.hooksPath` is set by composer's
  `post-install-cmd`, so the hook arrives with the dev dependencies rather than
  in a setup step somebody has to be told about.
- A failure of the command is not a failure of the commit. `vendor/` may be
  absent and `bin/cli` may exit nonzero; CI holds the same relation either way,
  and a hook that blocks a commit it cannot repair is worse than the drift.
- Rejected: writing the card in `typo3_feedback_record`, which would remove the
  step instead of guarding it. `src/Feedback/` is the channel both halves share
  and `src/Upkeep/`, which owns what a todo is, already reads it — the card
  written from there would make that a cycle.

## Assumed

- That `composer install` has run in every checkout somebody commits from. One
  that never installed the dev dependencies has no hook, and nothing says so.
- That the hook is not bypassed. `git commit --no-verify` is one flag away, and
  a worktree created before this commit keeps the config it was made with.
- That `bin/cli todo:sync` keeps printing one written path per line. The hook
  reads that output to know what to stage, and no test holds the format.

## Wrong if

- Feedback arrive with no card again and the commit came from a checkout where
  the hook was never enabled. Then the enabling step is what to remove — the
  hook would have to be something the repository cannot be committed to without.
- The staging surprises somebody: a commit comes out carrying a file the session
  did not write, and the card is wrong because the feedback was meant to be
  trimmed or closed in the same change.
- `bin/cli todo:check` stops reporting unserved feedback because the hook is
  assumed to have handled it. The hook covers the commits made here; the check
  covers the ones made anywhere else.

## Covered by

- `TodoTest::everyOpenFeedbackIsOnTheBoard`
