---
id: D-AUD-012
date: 2026-08-19
status: open
---

# D-AUD-012 — The second call of the entry point is an imperative of its own

**`typo3_task_guide` is told rather than described: the instructions say to call
it, where they said what it would give.**

Two calls stood in one paragraph, one of them a sentence the reader is asked to
do and one a sentence about a tool, and a run counted them eleven to one.

## Evidence

- The benchmark of 2026-08-19,
  [`D-SKL-033`](../task-skills/skl-033-activation-is-the-clients-and-the-order-after-it-is-what-this-server-holds.md)
  is where it is recorded: seventeen project tasks in Claude Code 2.1.234 on
  `claude-opus-5`, eleven `typo3_project_describe` calls and one
  `typo3_task_guide`.
- Both names stood in `instructions.start` of `knowledge/server-scope.json`, one
  paragraph apart, in the same context window of the same session. So the
  channel, the client, the model and the task set are held constant across the
  eleven and the one, and what differs is that the first sentence tells the
  reader to call and the second says what calling would give.
- The imperative is what this server publishes already. Step 3 of
  [`skills/base.md`](../../skills/base.md) says "Run it in every session, this
  skill's own tasks included" — on a channel a session reaches only by
  activating a skill, and `skills_used` is empty on all eighty-two rows of the
  same benchmark.
- The call that did fire ends without handing on. Read on 2026-08-19,
  `src/Tool/ProjectDescribe.php` names `typo3_rule_lookup` for the guides it
  lists and does not name `typo3_task_guide` anywhere in its description, its
  schema or its text.
- The room. Measured the same day, the worst assembly — the stale-skills notice
  with nothing excluded — stood at 2026 characters of the 2048
  [`R-ANS-013`](../../requirements/answers/ans-013-the-instructions-fit-what-a-client-keeps.md)
  holds. The rewrite costs 2 and it stands at 2028.

## Decided

- The sentence becomes "Then call typo3_task_guide for the workflow the task
  belongs to; it hands the parts that have their own workflow to the skill that
  owns them." The mood is the whole change.
- The sentence does not move. `skills/base.md` keeps the warning about a check
  the repository does not declare inside step 1 and the guide after it, and
  ahead of that warning the guide would separate "a check you recommend" from
  the commands it qualifies by a whole sentence.
- Not "in every session" beside it, which is `base.md`'s phrasing. It is about
  eleven more characters of the twenty-two that were left, and "the workflow the
  task belongs to" already says every task has one. A rewrite of one sentence
  displaces nothing; that addition would have to.
- Not the project answer naming the guide. It is the channel with the better
  argument on this run — the call that fired eleven times — and it is a change
  to an answer rather than to a sentence, so it is queued as
  `todo/open/2026-08-19-140000` rather than taken here.

## Assumed

- That the mood is what the eleven to one measures. The two sentences differ in
  position as well, one run separates neither, and nothing here can drive a
  second.
- That a client acting on the first imperative acts on a second one in the same
  paragraph. Nobody has watched a paragraph carrying two.

## Wrong if

- A run of the same shape counts the two calls again and the ratio has not
  moved. Then the mood is not what the placement bought, and the project answer
  is the lever left.
- A session reports calling `typo3_task_guide` for a task with no workflow to
  give it, and the answer costs it a turn. Then the descriptive wording was
  carrying a condition and the imperative overclaims.

## Covered by

- `ScopeTest::bothCallsOfTheEntryPointAreToldInTheImperative`
- `ScopeTest::theInstructionsFitWhatAClientKeeps`
