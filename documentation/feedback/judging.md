# Judging a feedback

A feedback is a session's report about this server, left by an agent working
somewhere else. What it is *not* is a work item: it names what went wrong from
where that session stood, and what to do about it is a judgement nobody has
made yet. This page is that judgement — what is asked of a feedback, in which
order, on what evidence, and which of the answers may be given without asking
first.

[readme.md](readme.md) says where a feedback lives and what happens to it once
it is worked off. This is the step between the two.

## The one question

Every feedback gets the same question, and it is not whether the feedback is
right:

**Could anything about this server have prevented it?**

Half the feedback this server receives is a session criticising its own work —
it did not consider Extbase, it violated the language-file rules, it never
activated the testing skill. Read as self-criticism those are somebody else's
laundry. Read as the question above they are a list of gaps this server could
have closed and did not, which is the most valuable half of the corpus.

Whether the self-criticism is accurate is not assessed, and cannot be: the
session was there and the reader was not. Only the lever is assessed.

## The ladder

The question is asked in five steps, cheapest first. Each step has evidence
that can be checked in this repository, and the first one that answers stops
the descent. A feedback that is not walked down the ladder is guessed at — and
the guess is always step one, which is why gaps get a fourth entry written next
to three that already exist.

### 1. Gap — the answer is not here

Two halves, told apart by what is missing rather than by what the feedback asks
for.

**1a — the knowledge is missing.** `bin/cli hints:probe "<the feedback's own
query>"` reaches nothing, and a search of `knowledge/` and `skills/` confirms
it. Becomes the work of establishing what actually holds — against
`.checkouts/`, and the manual after it — and writing that. Never the feedback's
own suggestion copied into `knowledge/`: its author was guessing about TYPO3
exactly as much as the judging run would be.

**1b — the shape is missing.** The answer is in principle available here, and
there is no way to get it in the form the task needed. **A tool answers a
question, a skill orders a task.** Where a tool is missing, an answer cannot be
had. Where a skill is missing, the answers are all available and nothing says in
which order to ask for them.

Neither needs the feedback to ask for it, and it usually will not: the session
reporting the cost does not know what this server could offer. What triggers 1b
is **what the session did instead** — every "I had to read it by hand", every "I
established this from my own knowledge", every call repeated with different
arguments. The debrief prompt asks for those, so almost every feedback states
them.

*Missing tool.* The five verbs in [AGENTS.md](../../AGENTS.md) make the
diagnosis precise, since the verb is what tells a caller the shape of an answer.
The bootstrap_package sweep is exactly this: `typo3_changelog_lookup` matches
title words, and enumerating every deprecation of a version is a `list`. Not a
broken lookup — a missing verb.

*Missing skill.* Three signals, and the third is the strongest because no single
feedback carries it: a session that invented the right order itself; a session
that went in an order that cost it the task; and the same sequence arrived at
independently by two sessions. That last one is the evidence
[writing-a-skill.md](../clients/writing-a-skill.md) says nothing can read off a
file — *that a domain earned a skill at all*. Its bar still has to be cleared;
the feedback shows it has been reached, not that it can be skipped.

Both are queued rather than closed on the spot: a tool and a skill are
contracts, and a skill is installed into somebody else's project, where a
mistake is not corrected by the next release.

**The category is not the answer.** `tool-gap`, `missing-knowledge`,
`wrong-answer` are how the reporting session saw it from where it stood, with no
view of this repository. A feedback filed as `missing-knowledge` is regularly a
routing failure, and one filed as `tool-gap` is regularly a rule that exists and
never arrived. The ladder is walked from the observation, not from the front
matter.

### 2. Delivery — it is here and never reached the session

**Evidence:** the rule exists, but it is not in the skill that was active, not
in the `instructions` sent at initialize, and reachable only through a tool
nobody called.

`bin/cli hints:coverage` is this step read across the whole corpus: on
2026-08-01 it reported 46 of 64 hints that no scenario prompt reaches at all.

**Becomes:** placement — the rule moves to where the task actually passes.

### 3. Routing — the right skill or tool exists and did not fire

**Evidence:** `knowledge/task-intents.json`, the `routing` block of
`knowledge/server-scope.json`, and the skill's own trigger.

**Becomes:** a routing entry, or a trigger the task shape actually matches.

### 4. Wording — it was delivered and did not take

**Evidence:** the wording itself. Written as a recommendation where it needed to
be a rule; buried below the part that answers the common case; ambiguous enough
that two readings are defensible.

**Becomes:** a rewrite. This is the cheapest fix on the ladder and the one most
often mistaken for step 1.

### 5. Decision — everything worked as designed, and the design is the price

**Evidence:** an entry in `decisions/` whose **Wrong if** this feedback
satisfies. A feedback from practice *is* the event a **Wrong if** describes, and
nothing reads them against each other today.

The signal is usually two feedback rather than one — the same property reported
as a strength by one session and as a cost by another. Both are then evidence,
and neither is wrong.

**Becomes:** a paragraph in the decision, and a question for the person who
maintains this repository. Never a change made quietly.

## What the ladder costs

Sources are asked in the order [AGENTS.md](../../AGENTS.md) already sets for
settling a question, and the cheap end answers most feedback:

|     | Source                       | Cost                           | Answers                                            |
| --- | ---------------------------- | ------------------------------ | -------------------------------------------------- |
| 0   | `bin/cli hints:probe`        | milliseconds                   | is the answer here at all                          |
| 1   | `knowledge/`, `skills/`      | cheap                          | where exactly, and in the right skill              |
| 2   | `.checkouts/`                | local                          | whether what the feedback claims about TYPO3 holds |
| 3   | `typo3_documentation_lookup` | network                        | what the official manual says                      |
| 4   | the open web                 | most expensive, least reliable | only where 0–3 give nothing                        |

Steps 2 to 4 are owed to any feedback that makes a claim about TYPO3 itself — a
changelog number, a deprecation, a version boundary. A feedback taken on trust
becomes a knowledge entry written with the confidence of a reading and the
substance of a guess, which is the one failure nothing downstream can detect.

## The answer names the gap, not the fix

A judgement ends at the diagnosis: which step of the ladder, on what evidence,
and what is missing — not what the entry that fills it will say.

That is a limit rather than an omission. The judging run has established nothing
about TYPO3; it ran a probe and a few searches over this repository, which is
what makes it cheap enough to walk the corpus at all. A solution named from that
position is recalled, not read — and the todo that follows copies it down, so
the guess ends up in `knowledge/` with a verified entry's authority and nobody
left who would check it.

The expensive half belongs to the todo anyway. So the outcomes below read as
*what the work is*, not *what the answer is*: the first concrete step of such a
todo is research, and the writing comes after it.

## The answers

Six, and only four of them may be reached without asking.

### Closed on the spot — *autonomous*

Only where there is nothing left to establish: the wording of a rule that is
already there, moving one to where the task passes, a routing line onto a skill
that exists. The change is made in the same run and the feedback is archived by
the commit that makes it.

Two things put a feedback on the other side of that line, and either is enough:

- **The change touches `src/`, a tool's declared schema, or a skill's
  contract.** Those are reviewed rather than improvised.
- **Something about TYPO3 has to be looked up.** Then it is a todo, however
  small, because the run that judged it has read nothing but this repository.

### Already answered — *autonomous*

The feedback describes a version of this server that no longer exists. Re-run
its query; where today's answer is correct, the feedback is answered and the
commit says which query was run and what came back, so the judgement can be
disagreed with.

### Queued — *autonomous*

The change is real and too large for the spot: it touches code, a schema, a
contract, or it needs a decision. A requirement records what must hold, a todo
records the next concrete step, and the feedback stays open until the commit
that implements it archives it.

### Trimmed — *autonomous*

Part of the feedback is answered and part is not. The answered part goes, the
rest stays open.

### Proposed — *needs an answer*

Nothing on the ladder produced a lever worth pulling, or the cost is out of
proportion to what it buys. That is a legitimate outcome and not one this
process may reach on its own.

A feedback is discarded by nobody here. What is written instead is the proposal:
which step of the ladder it reached, what evidence was found, what the change
would cost, and why it is not recommended. It waits until it is answered.

### Contradicts a decision — *needs an answer*

Step 5. The feedback is recorded against the decision it bears on, and the
question goes up. What comes back is either a decision that stands with the cost
now written into it, or one that is revised — and both are answers the feedback
earned.

## One at a time, and where the answer is written

Every open feedback has a card on the board, written by `bin/cli todo:sync`, and
`bin/cli todo:next` hands over one card like any other todo. A card nobody has
judged carries no priority and therefore sorts below everything somebody has, so
the oldest unjudged feedback is what comes up once the decided work is done. A
run is one judgement, and the loop ends when nothing is unjudged rather than
after a fixed number.

What that costs is the run that saw several at once: a feedback correcting three
earlier ones, or the same gap reported by four sessions, is a relationship no
single judgement can state. `decisions/` is what carries it. A judgement made
against an entry is written into that entry, and where it establishes something
no entry says yet, a new one is created — so what a run decided is readable
where decisions already are, rather than in the commit that made it, which is
the one place nobody can search. There is no journal beside the archive: a
second list of the same judgements is a second thing to keep true.

A **skip** is the exception and stays out of both. It lasts one pass, is written
down nowhere, and is not a state a feedback can be left in — a feedback that
deserves a state gets one of the six answers.

## The three states a feedback is in, and what holds each

A judgement does not close a feedback. It turns it into work, and the work is
what closes it.

| Where it is                   | What that means            | What holds it                              |
| ----------------------------- | -------------------------- | ------------------------------------------ |
| open, no todo serves it       | nobody has judged it       | `bin/cli todo:check` and `todo:sync`        |
| open, a todo serves it        | judged, the work is queued | `Todo::serves()`, which `feedback:list` marks |
| `feedback/archive/`           | the improvement landed     | `bin/cli feedback:archive`, in that commit  |

The middle state is the one worth being exact about. `typo3_feedback_list`
answers an agent somewhere else, and archiving is what that agent reads as
`closed`; a feedback closed the moment somebody decided about it would tell a
session its report was dealt with while the thing it reported is still there. So
the archive waits for the change, which is what AGENTS.md has always said, and
`Todo::unreadable()` enforces it from the other side — a todo serving an
archived feedback is reported as a problem, so archiving early costs one error
per todo the judgement derived.

**The invariant:** a commit that judges a feedback either archives it or leaves
at least one todo serving it. Anything else drops it back to unjudged, where the
next `bin/cli todo:sync` writes it a fresh card and the judgement is lost.
"Nothing to do" is therefore not a special case but the *close* answer:
archive in the same commit.

What cannot be checked is whether the judgement reached `decisions/`. A state
check cannot see it, and a judgement may legitimately confirm an entry without
changing a file. It stays the second **Wrong if** of
[`D-FBK-012`](../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md),
watched rather than held.

A feedback that **cannot** be judged is not closed either. The card moves to
`todo/waiting/` with the question in a `**Waiting on:**` line, where it still
serves the feedback — so `todo:sync` writes no second card, and no session is
handed a question nobody can answer.

## What a todo may not repeat

A todo that serves a feedback does not restate it. It carries what the feedback
does not: the answer — which step of the ladder, on what evidence — and the next
concrete step. One or two sentences, not a paragraph.

Otherwise the same account sits in `feedback/`, in `todo/` and, once a
requirement is written, a third time in `requirements/` — three places to
maintain, and any one of them can be brought up to date and leave the other two
saying something else. `bin/cli todo:next` prints the feedback a todo serves along
with it, which is what lets the todo carry only its own half: the feedback is
what happened once, the requirement is what must hold from now on, the todo is
what to do next.
