---
id: D-DOC-061
title: "A todo's id is derived rather than counted"
date: 2026-08-27
status: open
coveredBy:
  - CliTest::aBranchIsTheTodosIdAndNothingElse
  - CliTest::aTodoIsNamedByItsIdAndNotByTheWorktreeHoldingIt
---

# D-DOC-061 — A todo's id is derived rather than counted

**A todo is cited by `T-<yymmdd>-<hash>`, which is also its filename prefix.**

A filename of seventy characters is not something a commit message can name, and
an id allocated by counting what exists collides between worktrees.

## Evidence

- Two branches cut from the same `main` both wrote `D-ANS-114` on 2026-08-27,
  because each allocated the next id by counting the entries it could see. One
  was renumbered to `D-ANS-115` by hand at merge time.
- Todos are claimed in batches — three that run — so a counted id collides more
  often than a decision's does, not less.
- The cards written that run are stamped `2026-08-27-001800` and
  `2026-08-27-001900`. They are a minute apart by luck: `typo3_feedback_record`
  writes a card per feedback, and two agents recording in one second collide.

## Decided

- **The date orders and the digest separates.** `<yymmdd>` is what
  `todo/readme.md` reads the queue by after the priority in each head; the
  digest of the creation instant, the slug and a few random bits is what two
  writers in one second cannot both produce.
- **The id is the filename prefix.** The date is carried once rather than
  written beside a hash, and `2026-08-27-001900` goes.
- Rejected: a counter. A number allocated by reading what exists is what put
  `D-ANS-114` on two branches, and it is the half of an issue number that does
  not survive parallel work.
- Rejected: an issue number from GitHub. It cannot collide and it is visible off
  this machine, but the queue would stop being readable from the checkout alone,
  which is most of what `todo/` is for.

## Assumed

- That six characters of digest are enough for a queue of this size, where a
  collision is two cards written in one second whose slug also matches.

## Wrong if

- The hash turns out to be what nobody cites, because the slug is what a person
  reads and `T-260827-a3f9` is what only a command uses.
- The same reasoning is wanted for `decisions/` and `requirements/`, whose ids
  are counted and are what this entry's evidence is drawn from.

## Since then

On 2026-08-27 the maintainer read `git worktree list` during a three-todo run
and could not tell which todo each worktree held. `Todo::branch()` stripped the
id off the file name and kept the slug, so the branch and the directory named
after it carried the half no command reads.

That is the first **Wrong if** landing the other way up. It expected the hash to
be what nobody cites; what happened is that the id is what every command takes
and the slug is what none of them does. So the slug goes rather than moving
behind the id: a todo is named `T-260824-5867.md`, its branch is
`todo/T-260824-5867`, and the worktree is named after the branch. What the work
is about is the title inside the file, which every listing prints.

Keeping the slug behind the id was rejected. A listing that names the work reads
better, but the name would then move when somebody retitles the todo, and two
todos sharing a slug would derive one branch — `TodoClaim` reads a standing
branch as a todo somebody has in hand, so the second would leave the queue for
as long as the first was being worked.

The renaming waited for that run's worktrees to come home. `TodoNext` and
`TodoClaim` decide a todo is in hand by deriving its branch and looking for it
among the standing ones, so a derivation that moves while one is up offers work
already in flight a second time.
