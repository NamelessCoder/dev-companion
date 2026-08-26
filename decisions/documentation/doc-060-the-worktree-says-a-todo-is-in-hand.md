---
id: D-DOC-060
title: 'The worktree says a todo is in hand'
date: 2026-08-27
status: open
coveredBy:
  - CliTest::aWorktreeWithUncommittedWorkIsNotDropped
  - CliTest::droppingAWorktreeDeletesABranchThatCarriedNothing
  - CliTest::droppingAWorktreeKeepsABranchThatCarriesWork
  - TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt
  - TodoTest::aWorktreeOnNoTodosBranchHoldsNothing
  - TodoTest::whatIsInHandIsTheTodoAWorktreeStandsOn
---

# D-DOC-060 — The worktree says a todo is in hand

**A todo is in progress by having a worktree, and `todo/progress/` goes.**

The card there is a third copy of a fact `git worktree list` and the branch name
already carry.

## Evidence

- Three todos were claimed on 2026-08-27 into three worktrees. Each was said
  three times: a card in `todo/progress/`, a branch `todo/<slug>`, and a
  worktree of the same name.
- One of the three sessions did the whole job and stopped without committing.
  Its worktree looked like the two that were working, and `todo:home` refused on
  the dirty tree.

## Decided

- **The worktree is the marker.** `todo/progress/` goes, and with it the claim
  commit that carried the cards onto `main`.
- **`todo/waiting/` stays.** It holds the question the work is blocked on, which
  no worktree holds.
- Rejected: keeping a card for the state a worktree cannot hold — working,
  abandoned, or finished but uncommitted. A branch's last commit date answers
  the first two, and `todo:home` already refuses the third.

## Assumed

- That in-progress state does not need to be visible away from the machine the
  worktrees are on. The claim commit on `main` is what made it visible today,
  and dropping it is what buys the single copy.
- That a session on another machine never claims from this queue.

## Wrong if

- A worktree is left behind by a session that died, and nothing tells it from
  one being worked — the state `todo/progress/` carried a date for.
- Somebody wants to see what is in hand from a clone or from GitHub, where no
  worktree of theirs exists.
