---
id: D-FBK-046
date: 2026-08-14
status: open
---

# D-FBK-046 — The check that catches a duplicate id names the files and the command

**The assertion that catches a duplicate id names both files and
`bin/cli decisions:renumber`, because its message is all any reader of that
failure gets.**

The collision is the one failure this procedure predicts, and the command that
repairs it was written for exactly this moment — `D-DOC-015`. What arrives at
the session is a size mismatch between two counts.

## Evidence

- Reproduced on 2026-08-14 by copying one entry in `decisions/feedback/` under a
  second name: `two decision files claim the same id` and
  `Failed asserting that actual size 335 matches expected size 336`, at
  `tests/Unit/DecisionsTest.php` line 40. It names neither the id, nor either
  file, nor the command.
- `TodoHome::bring()` prints the last 30 lines of a red `composer ci` and adds
  nothing to them. So a caller who came through `bin/cli todo:home` reads that
  message and the four lines around it, and a session that ran the suite in its
  own worktree reads the same.
- The repair is on the page the procedure is documented on, under *what is
  dangerous is the renumbering, not the collision*. The reporting session
  renumbered by hand three times before reaching it, which is the half that
  paragraph warns about.
- Two feedback from this checkout report it. `2026-08-13-224118` is the
  refusal's shape; `2026-08-13-234208` measured three rounds — 5 of 24 branches
  colliding, 10 `decisions:renumber` calls, 30 `todo:home` invocations for 24
  branches.
- The same asymmetry has cost once already, and `todo:home` exists because of
  it: a session sent to bring three branches home read the page whole and then
  `TodoClaim.php` whole, 46 KB, to find out what four git commands were.
- `tests/Unit/RequirementsTest.php` line 41 carries the same assertion for
  requirement ids, and there is no `requirements:renumber` to name.

## Decided

- Step 2 of the ladder, delivery. Nothing is missing: the command exists, the
  page states when to run it, and neither reaches the moment the collision is
  found.
- The message is where it lands, rather than a branch in `todo:home` that reads
  the tail it prints. The assertion is the one place every reader passes —
  `todo:home`, a suite run by hand in the worktree, and CI — and a matcher over
  another command's output would be a second copy of the same knowledge, kept
  true against a message it does not own.
- What it says is the id, both paths, and `bin/cli decisions:renumber <id>`.
  Which of the two entries is already on `main` is git state and the suite does
  not read it; the caller knows which one their branch added, and
  `git diff main -- <file>` is what the command's own output already sends them
  to.
- The requirement assertion gets the two paths and no command, because there is
  none to name. Naming the files costs the same line either way, and the move by
  hand is what `D-DOC-015` left open there.
- Not reserving the ids at `todo:claim`, which is what the second feedback asks
  for first. `D-DOC-015` weighed that and rejected it — a claim cannot know
  which group it will write into, so it reserves in every one — and reopening it
  is that feedback's judgement rather than this one's.

## Assumed

- That the message reaches the caller through the tail `todo:home` prints. It is
  in PHPUnit's failure list, which is inside 30 lines for a run with one
  failure, and the reproduction above is that run.
- That a session handed both paths runs the command rather than a search and
  replace over the id. `D-DOC-015` already carries what that costs.

## Wrong if

- A session renumbers by hand with both paths and the command in front of it.
  Then the message is not the lever, and what is owed is the fallback both
  feedback name second: `todo:home` running the renumber itself, which is the
  one place that knows both which branch it is rebasing and what `main` holds.
- Another failure in the same run pushes the message out of the 30-line tail, so
  the caller who came through `todo:home` reads a truncation and the placement
  bought nothing there.
- A requirement id collides and the two paths are not enough, because moving one
  by hand is the dangerous half. That is `D-DOC-015`'s fourth **Wrong if**
  reached from this side.

## Covered by

- `DecisionsTest::aDuplicateIdNamesBothFilesAndTheCommandThatMovesOne` and
  `RequirementsTest::aDuplicateIdNamesBothFilesAndThatNothingMovesOne`. What the
  message says is held by a test rather than by reading it, because the checkout
  the assertion fails on is the one checkout where nothing collides —
  `Decisions::duplicates()` finds the files and `Decisions::collision()` writes
  what the reader gets.
