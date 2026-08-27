---
id: D-ANS-091
title: The project answer leaves the second call to the instructions
date: 2026-08-21
status: open
---

# D-ANS-091 — The project answer leaves the second call to the instructions

**The imperative to call `typo3_task_guide` stays in the `instructions`, where a
run recorded it being acted on, and the project answer keeps the onward pointers
it already has.**

One run counted the two calls eleven to one, and what it names as the lever is
the sentence rather than the answer of the call that fired.

## Evidence

- **The count.** The benchmark of 2026-08-19,
  [`D-SKL-033`](../task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md):
  seventeen project tasks, eleven `typo3_project_describe` calls and one
  `typo3_task_guide`. Read again in this checkout on 2026-08-21,
  `src/Tool/ProjectDescribe.php` names `typo3_rule_lookup` for the guides it
  lists and carries the string `task_guide` in no description, no schema and no
  line of its text.
- **The position the sentence would take has four sessions on record and routed
  none of them.**
  [`D-ANS-061`](ans-061-an-answer-that-names-a-document-hands-it-over.md)
  is where they are read, and its second **Assumed** — that naming a thing in an
  answer is enough — has been unmet since 2026-08-07. Three of the four held
  this same answer's guides listing, which is an onward name at the foot of it
  naming a tool call: `feedback/archive/2026-08-08-224406` ran one search
  instead, `feedback/archive/2026-08-17-205945` called `typo3_rule_lookup` at no
  point in the session, and `feedback/2026-08-19-094457` is the run of
  2026-08-19 itself, which received sixteen ids at the start of its audit, read
  none of them during it, and found the catalogue afterwards through a
  mis-transcribed id.
- **Both fixes that worked moved the name to the step that needs it**, away from
  the orientation answer:
  [`D-SKL-030`](../task-skills/skl-030-a-review-surface-names-the-lookup-that-can-answer-it.md)
  and
  [`D-SKL-045`](../task-skills/skl-045-a-build-workflow-names-the-guide-at-the-step-that-needs-it.md).
- **The `instructions` are the one channel this run recorded being acted on.**
  Eleven of the seventeen tasks did what the paragraph's first imperative says,
  so the channel reaches the model and the sentence is obeyed — `D-SKL-033`
  reads the eleven that way when it weighs the project's own agent instruction
  file and leaves it untaken.
- **The rewrite on that channel is two days old and unread.**
  [`D-AUD-012`](../audience/aud-012-the-second-call-of-the-entry-point-is-an-imperative.md)
  turned the second sentence into an imperative on 2026-08-19 for two characters
  of the twenty-two the budget had left, and no run has counted the two calls
  since.
- **What `D-SKL-013` recorded on the same run is narrower than a channel.** What
  did not fire there was a `Skill` activation, which `D-SKL-033` records as the
  client's to make and which `skills_used` shows empty on all eighty-two rows of
  that benchmark whatever named the skill. So it is evidence about activation,
  and the load-bearing evidence about an onward name is the guides listing on
  this very answer.
- **The cost is not what decides it.**
  [`D-ANS-087`](ans-087-the-project-answer-stays-whole-because-a-call-is-what-costs.md)
  measured this answer at 3267 characters of text and put the weight problem at
  94,000, so one sentence at its foot is inside what that entry allows.

## Decided

- **The sentence is not added.** It would take the one position in this answer
  that four sessions have passed over, while the same imperative already stands
  on the channel eleven calls were made on.
- **One lever at a time.** `D-AUD-012`'s rewrite is unmeasured, and a second
  sentence on a second channel makes the next count unattributable — that count
  is `D-AUD-012`'s own first **Wrong if**, and it is the only thing that can
  tell the two levers apart.
- **What would make the answer the lever, so nobody derives it again:** a run of
  the shape that **Wrong if** names, counting the two calls with the imperative
  in place and finding the ratio where it was. Then the sentence is written at
  the foot of the answer, beside the guides listing, and this entry is what it
  is written against.
- **The guides listing is untouched.** `D-ANS-061` put it there and
  `D-AUD-011`'s index routes to it; what the three sessions passing over it are
  worth is what `todo/open/2026-08-19-094457` was queued to judge, and that
  reading is not made here.

## Assumed

- **That the run which would settle the ratio can be had.** The benchmark is
  outside this repository and was read here rather than made here —
  [`D-EVI-008`](../evidence/evi-008-the-server-collapses-the-spread-of-a-lookup-rather-than-its-median.md)
  says as much of the comparison beside it — so the alternative to waiting may
  be no measurement rather than a later one.
- **That one imperative at the foot of an answer and a list of sixteen ids in
  the same place are one channel.** The session of `2026-08-19-094457` says what
  separates them: it did not know what the task would need yet, which is a
  reason to pass over a catalogue and not a reason to pass over a next step.
  Nothing has measured the two apart.
- **That the eleven are the instructions being obeyed rather than the model
  starting where it would have started anyway.** One client, one model, one
  benchmark.

## Wrong if

- A second run counts the two calls with the imperative in place and the ratio
  has not moved. Then the instruction channel is spent, the project answer is
  the lever `D-AUD-012` named, and this entry held it back one run too long.
- A session reports calling a tool because `typo3_project_describe` named it.
  That is `D-ANS-061`'s second **Assumed** met at last, and it would say the
  position carries a next step and fails only for a catalogue.
- A session reports finishing this answer without knowing what to call next.
  Then the answer owes a next step whatever the ratio says, and the count was
  never the question.

## Since then

The imperative is where this left it. `Coverage::instructions()` still carries
"Then call typo3_task_guide for the workflow the task belongs to; it hands the
parts that have their own workflow to the skill that owns them", and
`typo3_project_describe` has gained no imperative of its own.

All three **Wrong if** need a run rather than a reading, and none has been
recorded: `scenarios/runs/` holds the same three forward runs as before, none of
them a second count of the two calls, and no feedback since 2026-08-21 reports
either a tool called because the project answer named it or a session finishing
that answer without knowing what to call next.

### 2026-08-24 — asked for again, by a session that made neither call

[`feedback/archive/2026-08-24-140120`](../../feedback/archive/2026-08-24-140120-four-skills-matched-the-opening-request-almost.md)
proposes the sentence this entry declined, in its strongest form: that this
answer name the task skills fitting the repository kind it has just described,
because a skills list "costs one more field and lands in the very first call
every workflow makes".

That premise is what the session's own account takes back. It made no first
call. `typo3_project_describe` was reached several turns into the task and from
inside a skill that named it, so a skills field would have arrived after each of
the four skills it would have named was already needed. The position this entry
declines is one four sessions have passed over, and this is a fifth that would
have read it late.

No **Wrong if** is met. Nobody counted the two calls, because both were zero at
the opening; the session did not finish this answer without knowing what to call
next, because it did not begin with it; and no tool was called because this
answer named one. What the zero opening is evidence about is the instruction
channel rather than this answer, and it is read at
[`D-SKL-033`](../task-skills/skl-033-whether-a-skill-is-activated-is-the-clients-and-the-models.md).

### 2026-08-27 — a second zero opening, and the position it moves is not this one

[`feedback/archive/2026-08-25-114735`](../../feedback/archive/2026-08-25-114735-the-mandated-opening-calls-project-describe-and.md)
is `/home/benji/projects/typo3-cms` on `claude-opus-5[1m]`, five turns of core
work ending in a patch and a commit, with neither opening call made. It is the
strongest account yet of why: the session quotes the imperative back word for
word, calls it right, and names its own harness prompt — do the work through
`Bash` — as what the sentence lost to.

The first **Wrong if** does not fire on it. That one counts the two calls with
the imperative in place and asks whether the ratio moved; this is zero against
zero, so there is no ratio, and the reading of the 2026-08-24 row above holds
unchanged. Neither of the other two fires either: no tool was called because
this answer named one, and the session did not finish this answer, having never
begun with it.

What it does carry is a position this entry does not decide about. The one
answer the session did receive was `typo3_commit_message_guide`, in the turn the
work became a core patch, and it named nothing onward — which is
[`D-ANS-117`](ans-117-the-commit-draft-names-the-workflow-that-owns-the-commit.md).
The distinction is the one the 2026-08-24 row already drew: this entry declines
a sentence at the *opening*, and that one places a sentence at the *phase*, on
an answer a session under momentum still asks for.
