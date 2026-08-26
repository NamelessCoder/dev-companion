---
id: D-FBK-014
title: Every stage is a directory, and closing is none
date: 2026-08-02
status: confirmed
coveredBy:
  - TodoTest::aTodoThatNamesAQuestionIsParkedWhereNobodyIsOfferedIt
---

# D-FBK-014 — Every stage is a directory, and closing is none

**A todo is in `open/`, `progress/` or `waiting/`, and finishing it is a
deletion rather than a fourth place.**

Four states existed and three of them were directories. The queue was the one
addressed by the absence of one, which cost a special case in every place that
built a path.

## Evidence

- `todo/` on the day it was moved: three queued files loose beside `readme.md`,
  and four directories, of which `progress/`, `waiting/` and `recurring/` held
  todos and `reference/` held two pages that are not work at all. `Todo::read()`
  addressed the queue as the empty string, `Todo::parse()` built the path from a
  ternary on the kind, and putting a claim back wrote a bare `todo/` prefix. One
  state of four, spelled differently in three places.
- Nothing has ever been kept because it was done. `feedback/archive/` is the
  only directory here that holds what was closed, and
  [`R-FBK-002`](../../requirements/feedback/fbk-002-a-feedback-that-was-worked-off-stays-answerable-for.md)
  says what it is for: `typo3_feedback_list` answers an agent somewhere else,
  and that agent cannot read a deletion out of a commit.

## Decided

- The queue moves into `todo/open/`, so that where a file sits says which stage
  it is in for every todo alike. The number stays what it was, the place in the
  order.
- Closing is not a stage. A finished todo is deleted and the commit that
  finished it is the record; a directory of them would be a second thing to keep
  true, in a repository that has already twice split one shared file for that
  reason.
- `feedback/archive/` is not the counter-example it looks like. What decides is
  not how valuable the closed card was but whether whoever asks about it can
  read a commit — this repository can, and the agent that recorded a feedback
  cannot.
- `recurring/` and `reference/` stay beside the stages rather than among them:
  one has a clock and never closes, the other is not work. Moving `reference/`
  below `documentation/` was rejected in the working of this todo. AGENTS.md
  puts the machine-specific half there deliberately, where it can go stale
  without taking a scenario with it, and the page listing what is deliberately
  not queued only works where the session that would rediscover it is looking.

## Assumed

- That three stages are all a todo has. Nothing here reviews, approves or
  schedules, so the states are: nobody has it, somebody has it, nobody can start
  it.
- That deleting loses nothing anybody will look for. It is what this repository
  has always done, and no session has yet asked a question about a finished todo
  that `git log` could not answer.

## Wrong if

- Somebody writes a second list of what was finished, because a question keeps
  being asked that git cannot answer cheaply enough. Then closing is a stage
  after all and this entry is the reason it was not one.
- A fourth directory arrives beside the three and is neither a stage nor plainly
  beside them, which would mean the split into stages and non-stages does not
  hold the cases.
- `reference/` grows back into a backlog. It already holds one page bundling
  three unrelated items, which is the shape
  [`D-FBK-008`](fbk-008-one-todo-is-one-file-and-the-queue-is-in-the-names.md)
  split `todo.md` away from, and a second such page would say the store has a
  hole that people are filling there.

## Confirmed on 2026-08-22

Three stages and no fourth. `todo/` holds `open/`, `waiting/`, `recurring/` and
`reference/` on 2026-08-22, and `progress/` is absent because nothing is in hand
— an empty stage is a directory git does not keep, which is the same shape
`D-FBK-013` settled for the queue and costs nothing: `bin/cli todo:check` counts
`0 in hand` either way.

Nobody has written a second list of what was finished. Nothing under `todo/`
carries one, and the questions this session asked of the history — which todo
was worked when, what a commit closed — were answered from git each time.

`reference/` still holds one page, and it shrank rather than growing. Two of the
three items it bundled are no longer a machine's business at all, because
`bin/cli environment:create` makes those installations, so what is left is what
a scaffold cannot produce. That is the third **Wrong if** answered the other way
round: the page was read and emptied instead of a second one being started.

## Since then

There are two stages rather than three. `progress/` was the state a claim was
kept in, and `D-DOC-060` reads it off the worktree instead, so a todo somebody
has in hand is a file in `open/` that has not moved. What this entry decided is
untouched by that: where a file sits still says which stage it is in, and
closing is still a deletion.

The second **Wrong if** watched for a fourth directory arriving beside the
three. What arrived is the opposite — one of the three turned out to be
answerable from git — and the split into stages and non-stages held it.

