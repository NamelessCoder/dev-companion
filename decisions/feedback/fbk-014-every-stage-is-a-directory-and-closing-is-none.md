---
id: D-FBK-014
date: 2026-08-02
status: open
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
  ternary on the kind, and `Todo::release()` wrote a bare `todo/` prefix. One
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

## Covered by

- `TodoTest::aClaimIsOneMoveThatGoesBothWays`
