# What is written down, and where

Four files hold four different kinds of thing, and keeping them apart is what
keeps any of them readable. [AGENTS.md](../../AGENTS.md) has the rules; this is
what each one is for and how the work moves between them.

## Where a session starts

    bin/cli todo:next

One todo, not the queue and not everything nothing has answered for. Context is not free: a session
handed all of it reads for ten minutes and then starts by summarising what it
read.

Due is two questions. Has the clock come round, which the todo's `**Every:**`
answers — `session`, or a number of days, so five sessions in an afternoon do
not ask the same question five times. And is there anything to do, which the
todo's `**Run:**` command answers by exiting nonzero when it found work: the
sighting stops being the next thing the moment the last entry is named, without
anybody editing a todo to say so. What is owed a feedback or an unresolved entry
is
that judgement — a todo that takes it on, or the sentence saying why it stays as
it is — not the work itself, which is what the queue is for.

## Why the queue comes first

For a while it did not, and the effect is worth writing down. A todo that recurs
every session is due for as long as anything is unjudged, `next` asked the
recurring ones first, and feedback arrives from every session everywhere while
one session closes a handful. So every session opened on the same sighting, the
queue behind it was never reached, and entries sat in it untouched for as long
as `feedback/` was not empty — which is always.

The order is now: what has a clock, then the queue, then the sightings once the
queue is empty. It follows from what a judgement is. Judging a feedback is
deciding whether it becomes work; the queue is the work that decision produced.

Since 2026-08-02 the feedback are *in* the queue rather than behind it — one
card each, written by `bin/cli todo:sync` at `low`, which is below everything
somebody has judged to be worth more. The order above is unchanged and what
enforces it has moved: the priority does it now, where a group boundary did it
before. What is still behind the queue is the sighting of what nothing answers for, and
it is
reached when the queue runs dry — which now means that nothing decided is left
*and* nothing has arrived unjudged. Leaving it standing in order to judge more
feedback is deciding twice and doing nothing, and the pile it decides over grows
faster than any session can read it.

The second half of the same problem is the size of the reading, and the board is
what solves it now. `bin/cli todo:sync` writes one card per open feedback, and
`bin/cli todo:next` hands over **one** of them, like any other todo — a fresh
card is `low`, so the oldest unjudged feedback comes up once the decided work is
done. One query can be re-run in a session that also has work of its own;
sixty-seven cannot, and a session handed all of them closes whatever is easiest.
The portion was five until 2026-08-02, cut for a reader who could then only find
the judgements in the commit that made them. What carries that instead is
`decisions/`, which is where a judgement is written now — see
[judging.md](judging.md) and
[`D-FBK-012`](../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md).
`bin/cli feedback:list` is still the whole of it, for whoever wants the
overview.

What runs the sync is `.githooks/pre-commit`, on any commit that touches
`feedback/`: it writes the missing cards and stages them, so the feedback and
its card arrive together. `composer install` is what points git at that
directory, and the hook repairs rather than refuses — a commit made where it is
not enabled is caught by `bin/cli todo:check` and by CI, which is where the 20
cards of 2026-08-02 were found —
[`D-FBK-022`](../../decisions/feedback/fbk-022-a-feedback-brings-its-card-in-the-commit-that-brings-it-in.md).

What `next` can never do is run a feedback's own query against the server as it
is now. A feedback is evidence about a version of this server that may no longer
exist, and that reading is the session's.

## What happens to the todo it printed

Everything on this page is about the order of the work. What is read before the
todo at the front of it is changed, why the step is judged rather than executed,
where a question the work turns on is settled instead of recalled, and what is
asked because nothing here can answer it, is one page of its own:
[working-a-todo.md](working-a-todo.md), which `bin/cli todo:next` names with
every todo it hands over.

## Keeping the queue current

- A change of order is written down **before** the work starts, so the reason
  exists in the file rather than in a session that has ended.
- New work found along the way is added as a todo that names what it serves. If
  it serves nothing yet, it is an idea and belongs in the feedback that had it.

What the commit that finishes, trims or puts back a todo leaves behind is the
last section of [working-a-todo.md](working-a-todo.md); how a todo is written is
[todo/readme.md](../../todo/readme.md) itself.

## Asking a session to file its own feedback

A session in a client this repository can read leaves a transcript, and which
skills activated and which tools were called are evidence in it. A session in
somebody else's agent leaves nothing here. The only thing that can report such a
run is the session itself, and it will not do so unprompted — an agent that
finished its task considers itself done.

So it is asked, in a message of its own **after** the work is finished. Before
or alongside it, the debrief becomes part of the task: an agent told it will be
asked which tools helped calls tools to have an answer. The prompt below is
generic on purpose — it names no scenario, no skill and no tool, so the same
text works after a review, an implementation, or a question the server could not
answer at all.

What it asks for is the report, never the shape of the feedback. What each field
wants — one feedback per subject, the task named in the first line, where to
read the model identifier rather than remember it — belongs to
`typo3_feedback_record` and is written in its parameters, which is the only
documentation a client actually reads. A prompt that restates it is a second
copy that ages, and it would only reach the sessions somebody handed it to.

Half of what it asks about did not happen. The calls a session made are in front
of it; the skill that never activated, the tool it passed over and the question
it never asked leave nothing behind. A list that asks only what happened
confirms the surface the server already has —
[`D-AUD-003`](../../decisions/audience/aud-003-the-instructions-carry-the-entry-point.md)
is a session the published skill's body would have carried, which never loaded
it and went through Bash instead.

```text
The work is done. What follows is a debrief about the TYPO3 knowledge server you
had available, not about the repository you just worked in. Change no files.

That server records feedback about itself with typo3_feedback_record, and the
people who maintain it read them. Report the session you just had from your own
transcript, not from how it felt. Where that transcript begins at a summary of
earlier turns, say so and answer for the part you can see: what is missing from
your window is not the same as what did not happen.

- Which of its skills you activated, whether the skill fitted the task, and what
  in it you would keep or drop. Name the skill. If none activated, say so — that
  is a result.
- Which part of the task no skill carried: the step you worked out for yourself
  and would work out again in the next session. Say what you were doing at the
  moment one would have had to activate, and in which words — the request, the
  symptom, the files you had open. A skill is chosen on its description alone,
  so one that exists and stayed shut is a different finding from one that is
  not there.
- Which tool calls the task actually needed, in the order you made them, and how
  many round trips each answer cost. Name the ones you would not make again: a
  lookup that returned nothing usable, one you had to repeat with different
  arguments to get an answer, one that only restated what the previous answer
  already said.
- What you never put to it. A question you took elsewhere because you did not
  expect an answer, or a tool you read and passed over, is a finding the server
  has no other way of learning — it sees the calls that were made and nothing
  else. Name what you assumed, and whether it held.
- Where a name did not mean what it said: a tool whose name or description
  promised another answer, one you would not have found from its name, a word
  you searched with that the server spells another way. Say how you found the
  tools you did reach — from the list, from what the server told you at the
  start, or from a guess that landed. A name is what a client installed months
  ago still calls it by.
- Where something went wrong: an error, an answer that was incorrect, an
  argument or a schema you had to guess at, a call you could not complete.
- What the server saved you from — a wrong path you did not take, a file you did
  not have to read, an assumption it corrected before you acted on it. Be as
  concrete here as about the failures; what worked is what must not be broken
  later.
- What you had to establish elsewhere — from the checkout, from your own
  knowledge, from the web — that this server should have answered.
- What this list did not ask you about, and you would report anyway.

File what you find with that tool. What each of its parameters wants, it says
itself — read them there and fill in every one you can, including the model you
are running as.

Then tell me which feedback you filed and what each one says.
```

Paste it verbatim and add nothing — no tool names, no hint about what the last
session reported. The summary the agent gives afterwards is not what was
recorded; the feedback is, and `typo3_feedback_list` is where it is read back.

The one qualification it carries is
[`R-FBK-012`](../../requirements/feedback/fbk-012-a-debrief-reports-the-window-the-session-could-see.md).
The prompt is where the transcript is asked for, so it is where a session whose
transcript begins at a summary is asked to say so.

## Working a feedback off

    bin/cli feedback:archive feedback/2026-07-31-…-the-lookup-found-nothing.md

That moves it to `feedback/archive/` and stamps it `closed`. The commit that
moved it is read back: `typo3_feedback_list` with `status="closed"` lists the
archived feedback with the commit subject that closed each one, which is what
the agent that reported it can see. Write the subject so it answers "what came
of my feedback".

One feedback per commit where possible. When one change closes several, archive
all of them in that commit — one call, since they are one commit — and mention
them in the commit body.

## Why the archive is kept

A feedback is deleted nowhere. What it holds is a session's report about this
server — which skill activated, which calls the task actually needed, what it
had to establish from the checkout or from its own knowledge instead — and that
is evidence about this server that nothing else in the repository has. The
requirement it established says what must be true from now on; it does not say
what a session in somebody else's agent ran into on a Tuesday, and that is the
half worth reading when the next one runs into it too.

Keeping it is also what makes the closed half of `typo3_feedback_list` usable: a
feedback read from a commit was a filename and a subject, with the category, the
tools and the model gone with the file, so a query about one tool could only be
answered from the open half. Both halves are now the same files read the same
way.

The feedback worked off before the archive existed were restored into it from
the commits that deleted them, and those carry the commit that closed them in
their own front matter — `closed:`, `commit:` and `subject:`. They were all
moved in one commit, and that move says nothing about any of them; every
feedback archived since is answered by the commit that archived it.

## Where the answer goes

Archiving the feedback takes the question out of the open ones, and the commit
message records the answer. What outlives both goes to three directories, and
two of them are not this workflow's: `requirements/` and `decisions/` are the
record this repository keeps whether or not a feedback was what produced an
entry. How each one is written has a page of its own —
[writing-a-requirement.md](../requirements/writing-a-requirement.md) and
[writing-a-decision.md](../decisions/writing-a-decision.md).

- `requirements/` — what must be true from now on. A feedback is a question; the
  requirement it established has to keep holding while everything around it
  changes, so it is written down with what holds it to that: a test, or
  `not guarded`. A requirement that has been accepted but not yet implemented is
  in the same group, marked **open** — decided and not done. Add the entry in the
  commit that works the feedback off, and name the test in the same commit that
  writes it. An entry is deleted only when the requirement is withdrawn.
- `decisions/` — what the change rests on. When it rests on an assumption that
  could later turn out wrong, record what was assumed, what evidence there was
  at the time, and what would show it to be wrong. One decision is one file, in
  the group its id names, and the entry opens with the decision in one sentence.
  Not every commit earns one; a change nobody would need to reconsider does not.
  When an assumption is later disproved, correct the entry in place rather than
  deleting it — the wrong assumption is the useful part, because it names where
  the next one is likely to sit.
- `todo/` — the order of the work, and where the last session stopped. The other
  files say what must be true, what was asked and what was assumed; none of them
  says what to do next. A session can end anywhere, and the next one starts by
  reading this. One todo is one file: it names what it serves and what the next
  concrete step is, and is deleted when done rather than ticked. Where it sits
  says whether it is the queue, what recurs, what waits on an answer nothing
  here can give, or what is kept for reading, and a queued one carries the
  priority that decides how soon — that is what `bin/cli todo:next` reads and
  what `bin/cli todo:check` holds.

## What nothing fails on

The three states that mean unfinished are legitimate — a principle no test can
hold and a decision nothing has come back about are not defects — so no check
may fail on them, which is exactly why nothing read them for as long as they
existed.

`bin/cli unresolved:list` is that reading; `bin/cli todo:next` opens with it and
`bin/cli repository:check` closes with it. It names every requirement nothing
answers for, says whether a queued todo names it — read from what the queue
declares it serves, so the page listing what is deliberately *not* queued does
not count as having taken one on — and gives the oldest open decision as the one
the repository has moved furthest away from.

Standing on that list is not the problem. Standing on it with no todo naming it
is a decision nobody has taken, and taking it — a todo, or the sentence in
`decisions/` that says why not — is what a session owes it.
