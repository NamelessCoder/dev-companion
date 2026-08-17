---
id: D-FBK-011
date: 2026-08-02
status: open
---

# D-FBK-011 — The suite holds what one branch can be right about

**The generated group listing is held by `requirements:check` and
`decisions:check` and by nothing in `composer ci`.**

A session working one of several todos is told to leave that block alone,
because it is generated from every file in a group and a worktree can see only
its own new entry. The suite held it anyway, so the branch could be right or
green, never both.

## Evidence

- Measured on 2026-08-02: `status: confirmed` on `D-SCO-002` failed the listing
  assertion `DecisionsTest` carried, on `decisions/readme.md` and on
  `decisions/scope/readme.md`, and failed nothing else. Adding an entry has the
  same shape, the listing being short by one until the merge.
- The rule and the check were written for different runs and neither named the
  other: `working-todos-in-parallel.md` says the merge runs both index commands
  once and that `requirements:check` says so if it forgets, while `composer ci`
  runs the same assertion on every branch.

## Decided

- The assertion leaves both suites — it was `everyGroupListsWhatIsInIt` in each
  of them. It stays in the two check commands, which is where the merge
  procedure already runs it, and `repository:check` carries them.
- The line drawn is which run can be right about what: the suite holds what one
  entry is on its own — its id, its group, its date, its status, its fields, the
  tests it names — and the commands hold what only a checkout with every file in
  it can satisfy.
- Rejected: a test that reads which run it is in. A check that behaves
  differently on a branch is off exactly where the work happens, and the two
  behaviours are then a thing to keep in step.
- Rejected: letting a branch regenerate. Two branches adding to one group then
  insert conflicting lines into the same block, and the procedure rebases rather
  than merges — the conflict lands on whoever is second, per branch, for a file
  neither session wrote.
- The queue collision is not the same case and stays in the suite: two branches
  that pick the same number are each right on their own and wrong once merged,
  so `TodoTest` fails on `main`, which is where it can be fixed.

## Assumed

- A stale listing between merges costs a reader one missing line in an index,
  and nothing that reads it programmatically. That is why it is the half that
  may wait for a command.

## Wrong if

- A listing goes stale on `main` and stays there, because the merge procedure is
  the only thing that runs the command and a session working alone never does.
  Then the index belongs in a hook or in `todo:next`, not in a suite that
  branches cannot pass.

## Covered by

- Nothing: what this decides is that one assertion is absent from the suite, and
  the check it moved to is `bin/cli decisions:check`.

## Since then

The rejection above moved on 2026-08-18, and only the half of it about when. A
session regenerating mid-work is still rejected for the reason given: two
branches adding to one group insert conflicting lines into a block neither
session wrote, and the procedure rebases rather than merges. `todo:home` now
runs both index commands in the worktree after the rebase, where that objection
is gone — the branch already stands on what `main` has, and nothing rebases onto
one that is about to be fast-forwarded — and amends what they wrote onto the
branch's own commit.

What that replaces is the commit on `main` that used to carry the same rewrite.
37 of the 200 commits before that day were
`[TASK] Take up what the merged branches left for main to write`, each one
holding a listing line separated by a commit boundary from the entry it lists.
The claim a session left in `progress/` moved across with it, for the same
reason and into the same commit.
