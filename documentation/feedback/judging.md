# Judging a feedback

> **Draft.** This page is being written with the person who maintains this
> repository, and the points still open are marked **`OPEN:`** where they sit.
> Nothing below is settled until that marker is gone.

A feedback is a session's report about this server, left by an agent working
somewhere else. What it is *not* is a work item: it names what went wrong from
where that session stood, and what to do about it is a judgement nobody has
made yet. This page is that judgement — what is asked of a feedback, in which
order, on what evidence, and which of the answers may be given without asking
first.

[feedback.md](readme.md) says where a feedback lives and what happens to it once
it is worked off. This is the step between the two.

## The one question

Every feedback gets the same question, and it is not whether the feedback is
right:

**Could anything about this server have prevented it?**

That reframing is the whole of it. Half the feedback this server receives is a
session criticising its own work — it did not consider Extbase, it violated the
language-file rules, it never activated the testing skill. Read as
self-criticism those are somebody else's laundry, and they were treated as
such. Read as the question above they are a list of gaps this server could have
closed and did not, which is the most valuable half of the corpus.

Whether the self-criticism is accurate is not assessed, and cannot be: the
session was there and the reader was not. Only the lever is assessed.

## The ladder

The question is asked in five steps, cheapest first. Each step has evidence that
can be checked in this repository, and the first one that answers stops the
descent. A feedback that is not walked down the ladder is guessed at — and the
guess is always step one, which is why gaps get a fourth entry written next to
three that already exist.

### 1. Gap — the answer is not here

Two halves, and they are told apart by what is missing rather than by what the
feedback asks for.

**1a — the knowledge is missing.** `bin/cli hints probe "<the feedback's own
query>"` reaches nothing, and a search of `knowledge/` and `skills/` confirms
it. Becomes the work of establishing what actually holds — against
`.checkouts/`, and the manual after it — and writing that. Never the feedback's
own suggestion copied into `knowledge/`: the feedback is a report about this
server, and its author was guessing about TYPO3 exactly as much as the judging
run would be.

**1b — the shape is missing.** The answer is in principle available here, and
there is no way to get it in the form the task needed. Two things can be
missing, and they are not the same thing: **a tool answers a question, a skill
orders a task.** Where a tool is missing, an answer cannot be had. Where a skill
is missing, the answers are all available and nothing says in which order to ask
for them.

Neither needs the feedback to ask for it, and it usually will not: the session
reporting the cost does not know what this server could offer. What triggers 1b
is **what the session did instead** — every "I had to read it by hand", every
"I established this from my own knowledge", every call repeated with different
arguments to get an answer. Those are stated in almost every feedback, because
the debrief prompt asks for them.

*Missing tool.* The five verbs in [AGENTS.md](../../AGENTS.md) make the diagnosis
precise, since the verb is what tells a caller the shape of an answer. The
bootstrap_package sweep is exactly this: `typo3_changelog_lookup` matches title
words, and enumerating every deprecation of a version is a `list`. Not a broken
lookup — a missing verb.

*Missing skill.* Three signals, and the third is the strongest because no
single feedback carries it: a session that invented the right order itself, and
so the next one may not; a session that went in an order that cost it the task;
and the same sequence arrived at independently by two sessions. That last one
is evidence [writing-a-skill.md](../writing-a-skill.md) explicitly says nothing
can read off a file — *that a domain earned a skill at all* — and feedback is
where it comes from. Two core patch reviews in the open corpus ran the same
sequence and both praised it, and a third feedback proposes the skill outright.

The bar in [writing-a-skill.md](../writing-a-skill.md) still has to be cleared;
the feedback is what shows it has been reached, not a shortcut past it.

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

`bin/cli hints coverage` is this step read across the whole corpus: on
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
satisfies.

This is the step that points upward instead of producing work, and the reason
it exists is that a feedback from practice *is* the event a **Wrong if**
describes. Nothing reads them against each other today: `bin/cli backlog list`
names the oldest standing decision, but not whether one of the open feedback
contradicts it.

The signal is usually two feedback rather than one — the same property reported
as a strength by one session and as a cost by another. Both are then evidence,
and neither is wrong.

**Becomes:** a paragraph in the decision, and a question for the person who
maintains this repository. Never a change made quietly.

## What the ladder costs

Sources are asked in the order [AGENTS.md](../../AGENTS.md) already sets for
settling a question, and the cheap end answers most feedback:

| | Source | Cost | Answers |
| --- | --- | --- | --- |
| 0 | `bin/cli hints probe` | milliseconds | is the answer here at all |
| 1 | `knowledge/`, `skills/` | cheap | where exactly, and in the right skill |
| 2 | `.checkouts/` | local | whether what the feedback claims about TYPO3 holds |
| 3 | `typo3_documentation_lookup` | network | what the official manual says |
| 4 | the open web | most expensive, least reliable | only where 0–3 give nothing |

Steps 2 to 4 are owed to any feedback that makes a claim about TYPO3 itself — a
changelog number, a deprecation, a version boundary. Those cannot be settled
from this repository, and a feedback taken on trust becomes a knowledge entry
written with the confidence of a reading and the substance of a guess, which is
the one failure nothing downstream can detect.

## The answer names the gap, not the fix

A judgement ends at the diagnosis. Which step of the ladder, on what evidence,
and what is missing — not what the entry that fills it will say.

That is a limit rather than an omission, and it holds for two reasons. The
judging run has not established anything about TYPO3: it ran a probe and a few
searches over this repository, which is what makes it cheap enough to walk 69
feedback. A solution named from that position is recalled, not read — and the
todo that follows copies it down, so the guess ends up in `knowledge/` with a
verified entry's authority and nobody left who would check it. This repository
has a sentence for that outcome: *a guess written with the confidence of a
reading, because nothing afterwards can tell the two apart.*

And the expensive half belongs to the todo anyway. Establishing what holds costs
`.checkouts/`, the manual, sometimes the open web — put that in the sighting and
the sighting is back to five a session, which is the problem this process
exists to solve.

So the ladder's outcomes below read as *what the work is*, not *what the answer
is*: the first concrete step of such a todo is research, and the writing comes
after it.

## The answers

Six, and only three of them may be reached without asking.

### Closed on the spot — *autonomous*

Only where there is nothing left to establish. The wording of a rule that is
already there; moving one to where the task passes; a routing line onto a skill
that exists. The change is made in the same run and the feedback is archived by
the commit that makes it.

Two things put a feedback on the other side of that line, and either is enough:

- **The change touches `src/`, a tool's declared schema, or a skill's
  contract.** Those are contracts, reviewed rather than improvised — and a
  skill is installed into somebody else's project, where a mistake outlives the
  next release.
- **Something about TYPO3 has to be looked up.** A version boundary, a
  changelog number, what a class does today. Then it is a todo, however small,
  because the run that judged it has read nothing but this repository.

What is left is smaller than it sounds and still worth having: routing a
rewording through requirement, todo, queue and a later session costs more than
the rewording.

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
rest stays open. Never archive a feedback that was only half addressed.

### Proposed — *needs an answer*

Nothing on the ladder produced a lever worth pulling, or the cost is out of
proportion to what it buys. That is a legitimate outcome and it is **not** a
answer this process may reach on its own.

A feedback is discarded by nobody here. What is written instead is the proposal:
which step of the ladder it reached, what evidence was found, what the change
would cost, and why it is not recommended. It waits until it is answered.

### Contradicts a decision — *needs an answer*

Step 5. The feedback is recorded against the decision it bears on, and the
question goes up. What comes back is either a decision that stands with the
cost now written into it, or one that is revised — and both are answers the
feedback earned.

## What a todo may not repeat

A todo that serves a feedback does not restate it. It carries what the feedback
does not: the answer — which step of the ladder, on what evidence — and the
next concrete step. One or two sentences, not a paragraph.

The duplication that exists today is produced by a rule rather than by
sloppiness. [AGENTS.md](../../AGENTS.md) asks a todo's paragraph to be written
*in enough detail that someone who has read nothing else can start*, and where
the todo already names the feedback that describes the problem, that demand can
only be met by copying it. The same account then sits in `feedback/`, in
`todo.md` and, once a requirement is written, a third time in `requirements/` —
three places to write it, three to maintain, and any one of them can be brought
up to date on its own and leave the other two saying something else.

So the demand is met when the todo is **handed over** rather than when it is
written: `bin/cli next` prints the feedback a todo serves along with it.
Nothing is repeated, the reader still has everything, and the `**Serves:**`
line stops being a reference nobody follows.

Each of the three then carries only its own half, which is what
[feedback.md](readme.md) already says they are for: the feedback is what
happened once, the requirement is what must hold from now on, the todo is what
to do next.

## Why every answer is written immediately

Not at the end of a bundle, at the end of each feedback.

An honest judgement costs the feedback itself, usually a probe, a search,
sometimes a checkout read — call it fifteen thousand characters. A debrief of
twenty-one feedback therefore does not fit in one context, and it never did.
What happened instead is that a run got through six, ran out of room, and left
nothing behind: the answers were in the context and nowhere else, so the next
run read the same six feedback again and paid the same cost.

So the context stops being the state. Every answer is on disk before the next
feedback is opened, and where a run ends does not matter, because everything
before that point is recorded. This is the same property `feedback/` was given
from the start and for the same reason — `src/Feedback.php` says it plainly:
*one feedback per file, so concurrent agents never touch the same file*.

The trace is a **journal plus the commit**. One line per feedback — date, ladder
step, evidence, how it came out — and the commit that made the change is the
answer itself.

The feedback is not written to. Where a feedback **stands** is what says what
became of it, and a status field somebody edits is the one thing that could
disagree with where it is.

The journal is not bookkeeping. It is the only thing that carries any
relationship between feedback, which matters because feedback is judged one at
a time and no run sees more than one: the run that opens feedback 40 reads the
journal's forty lines, not the forty feedback. That is what catches the two
cases a single feedback cannot state — one feedback in the open corpus
explicitly corrects three earlier ones, and the same gap gets reported by
several sessions in a row. Both are invisible per feedback and obvious in a
list of answers.

It also makes the corpus readable as a whole for the first time: the same
diagnosis appearing five times is five lines under each other, and that is an
argument no individual feedback can make.

## Why a feedback is judged in its own context

The ladder is written to be executed, not discussed. Fixed steps, fixed
evidence per step, a fixed shape coming back — because the run that judges a
feedback is not the run that keeps the overview.

The main run never reads a feedback. It reads the list, hands each feedback (or
bundle) to a run with a fresh context, and gets back the few lines of the
answer: which step, which evidence, which outcome, which diff. It grows by ten
lines per feedback instead of fifteen thousand characters, and the expensive
research is paid by a context that is thrown away afterwards.

That is what makes "efficient" and "researched properly" compatible rather than
opposed. The depth happens somewhere that does not have to carry it afterwards.

## One feedback per run, in a loop

Nothing is bundled. One feedback is judged per run, the context is cleared, the
next run starts empty — and that repeats until there is no unjudged feedback
left.

The alternative was to hand over a session's worth at a time, and the size of
that portion was never the question. Five was chosen because a run can just
about manage five, which is a statement about what a context holds rather than
about the work; the corpus grows faster than that either way. A loop that
resumes has no portion size, so nothing has to be estimated: the run judges what
is in front of it, writes the answer, and ends. Where the context would have
filled, a new one starts instead.

What this gives up is reading several feedback from one debrief together, and
the journal is what pays for it — see above. What it buys is that the process
no longer has a size limit at all.

So `bin/cli feedback next` hands over **one** feedback, the oldest with no
journal entry, and exits nonzero while any remain. That is the same shape
`bin/cli next` already has for todos, down to naming the page that says how the
thing is worked.

**This is step 5 of the ladder applied to the process itself.**
[`D-FBK-5`](../../decisions/feedback/fbk-5-the-queue-is-worked-before-the-pile-is-sighted.md)
put the sighting behind the queue and set the portion at five, and
[`R-FBK-7`](../../requirements/feedback/fbk-7-the-work-already-judged-comes-before-judging-more.md)
holds it there. It did not merely turn out to be wrong — it named this exact
outcome as the condition that would show it wrong:

> **Wrong if:** `feedback/` keeps growing while the queue never empties, so the
> five are never handed over at all — then the sighting needs a place of
> its own rather than a position behind the queue.

It also recorded, as an assumption, *that the queue empties*, with the feedback
that nothing had run long enough to show it. The decision was taken on
2026-08-01 against 56 open feedback and 38 queued items; later the same day
there are 69 open feedback and 33 queued. The queue moved by five, the pile by
thirteen.

One day is a direction, not a proof, and it is written here as a direction. But
the answer the decision proposed for its own failure — *a place of its own
rather than a position behind the queue* — is what a loop with its own trigger
is. The reversal follows the decision's own reasoning rather than overruling it.

Both were right about the failure they saw, a sighting that printed 57 lines
before its own instruction, and both answered it by limiting the reading —
because a run's context was assumed to be where the work is held. Once the
answer is on disk before the next feedback is opened, that assumption is gone
and the limit costs more than it protects. The requirement changes with the
commit that builds this; the decision is corrected in place rather than
deleted, because the assumption it made is exactly where the next one is likely
to sit.

There is no clock. A cadence in days was the decision's own suggestion, and it
only makes sense while the sighting is a recurring todo stuck behind the queue —
a clock is then the trick that pulls it forward. A loop ends when nothing is
unjudged, which is a condition rather than a date, and a loop that resumes has
no deadline to miss.

## The order is arrival, and the only move is backwards

First in, first out. The oldest unjudged feedback is the next one, and that is
the whole of the ordering — no ranks, no priorities, no file that says what
comes before what.

One operation acts on it: **skip it for now and take the next one**. Something
turns out not to be the thing to do at this moment — it is more expensive than
it looked, it hangs on something else — and the loop moves on instead of
stopping on it.

A skip lasts for the pass it happened in, and nothing about it is written down:
the loop holds what it has skipped, and the next loop starts from the front
again. That is what "for now" means, and it is the whole reason a skip needs no
field, no date and no history.

What a skip is not is an answer, and keeping those apart is what stops the loop
from spinning. A skip **passes over**; a state **takes out**. A feedback
proposed to the maintainer has been judged and is waiting for an answer, so it
leaves the list until the answer comes — skipped instead, every later pass
would pick it up, propose it again, and do that forever.

So the same feedback landing at the front of every pass and being skipped every
time is not a flaw in the ordering. It is the list saying that this one needs a
state and a reason rather than another skip.

The sort key is arrival, which `feedback/` already carries in its filenames, so
there is nothing to maintain: no rank, no order file, no renaming. The queue is
what the directory already is.

## What this asks of `todo.md`

**`OPEN:` — the whole of this section.**

`todo.md` is the last shared log in a repository that solved this three times
over: `feedback/`, `requirements/` and `decisions/` are one file per thing, and
this one is 30,373 bytes holding 33 queued items. Finishing a todo means loading
all of it to delete a paragraph — at the end of a run, when there is least room
for exactly that, which is why finished todos stay.

It also makes two things this process needs impossible: several judging runs
cannot each add a todo without writing the same file, and the states below are a
parser in prose where they are a field in a directory.

The draft is `todos/`, one file per todo, same convention as the three
neighbours, with the order in a small file of its own rather than in the
positions of paragraphs.

**States**, which are what makes the queue readable:

| State | Meaning |
| --- | --- |
| *(none)* | the queue, in the order it has |
| `waiting` | blocked on an answer only the maintainer can give; carries the question |
| `review` | built and committed, waiting to be accepted |

Today a blocked todo goes to the end of the queue, where it looks like the
lowest priority in the repository while it is actually waiting for a person.
