---
id: D-GUI-2
date: 2026-07-29
status: open
---

# D-GUI-2 — The commit workflow is asked for, not inferred

**The commit workflow is an argument that defaults to `core`.**

A commit message carries no paths, and its subject text describes the change
rather than the repository it lands in.

`typo3_commit_message_guide` now takes `workflow: "core" | "project"`. Every
other tool that draws this line derives it — `Scope::isOutsideCore` reads the
paths and the task text. This one does not.

## Decided

- An argument, defaulting to `core`. A commit message carries no paths, and the
  one thing it does carry — the subject text — describes the change, not the
  repository it lands in. Inferring from it would be guessing from prose, which
  is exactly what R-SCO-1 exists to stop.

## Assumed

- The caller knows which repository they are committing in, and the pointer at
  the end of every answer is enough for them to find the other mode. The
  default is `core` because dropping rules must be something the caller asked
  for, never something a typo achieves.
- `[SECURITY]` stays refused for core work. The keyword exists in the core's
  history — the Security Team writes those commits — so its absence from the
  enum was a gap, but accepting it for a contributor would be a wrong answer
  with worse consequences than a missing one.

## Wrong if

- Agents commit in a project repository and never pass the argument, so the
  hard `missing-issue` error stays the normal answer there. The next step would
  then be for `typo3_task_guide`, which does compute `outsideCore`, to hand the
  workflow to the commit guide by naming it in the follow-up tool call it
  suggests.
