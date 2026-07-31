# What is written down, and where

Four files hold four different kinds of thing, and keeping them apart is what
keeps any of them readable. [AGENTS.md](../AGENTS.md) has the rules; this is what
each one is for and how the work moves between them.

## Where a session starts

    bin/cli next

That is the whole of it, and it prints **one todo** — the first that is due,
whole, with its own command already run. Not the queue, not the backlog, not the
five paragraphs of why that one is in front. Context is not free: a session
handed all of it reads for ten minutes and then starts by summarising what it
read. `bin/cli todo list` is the overview, for whoever wants it.

Due is two questions. Has the clock come round, which the todo's `**Every:**`
answers — `session`, or a number of days, so five sessions in an afternoon do
not ask the same question five times. And is there anything to do, which the
todo's `**Run:**` command answers by exiting nonzero when it found work: the
notes stop being the next thing the moment the last one is judged, without
anybody editing [todo.md](../todo.md) to say so. What is owed a note or a
backlog entry is that judgement — a todo that takes it on, or the sentence
saying why it stays as it is — not the work itself, which is what the queue is
for.

What `next` can never do is run a note's own query against the server as it is
now. A note is evidence about a version of this server that may no longer exist,
and that reading is the session's.

## Keeping todo.md current

Part of the work, not a step after it:

- The commit that finishes a todo **deletes that todo**, the way the commit that
  works a note off deletes the note. What it established is in `requirements/`
  by then, and the commit is the record that it happened.
- A todo that turns out to be half done is trimmed to the part that is left,
  with the next concrete step rewritten. One nobody can start from is worse than
  no todo.
- One paragraph, one step. Two steps are two todos, in the order they are in —
  a paragraph is printed whole, and a session that has to read three of them to
  find where to start is reading instead of working.
- A change of order is written down **before** the work starts, so the reason
  exists in the file rather than in a session that has ended.
- New work found along the way is added as a todo that names what it serves. If
  it serves nothing yet, it is an idea and belongs in the note that had it.

A session that ends with the file matching what is actually true has handed over
correctly, whatever else it did.

## Asking a session to file its own notes

A session in a client this repository can read leaves a transcript, and which
skills activated and which tools were called are evidence in it. A session in
somebody else's agent leaves nothing here. The only thing that can report such a
run is the session itself, and it will not do so unprompted — an agent that
finished its task considers itself done.

So it is asked, in a message of its own **after** the work is finished. Before or
alongside it, the debrief becomes part of the task: an agent told it will be
asked which tools helped calls tools to have an answer. The prompt below is
generic on purpose — it names no scenario, no skill and no tool, so the same text
works after a review, an implementation, or a question the server could not
answer at all.

What it asks for is the report, never the shape of the note. What each field
wants — one note per subject, the task named in the first line, where to read
the model identifier rather than remember it — belongs to
`typo3_feedback_record` and is written in its parameters, which is the only
documentation a client actually reads. A prompt that restates it is a second
copy that ages, and it would only reach the sessions somebody handed it to.

```text
The work is done. What follows is a debrief about the TYPO3 knowledge server you
had available, not about the repository you just worked in. Change no files.

That server records notes about itself with typo3_feedback_record, and the
people who maintain it read them. Report the session you just had from your own
transcript, not from how it felt:

- Which of its skills you activated, whether the skill fitted the task, and what
  in it you would keep or drop. Name the skill. If none activated, say so — that
  is a result.
- Which tool calls the task actually needed, in the order you made them, and how
  many round trips each answer cost. Name the ones you would not make again: a
  lookup that returned nothing usable, one you had to repeat with different
  arguments to get an answer, one that only restated what the previous answer
  already said.
- Where something went wrong: an error, an answer that was incorrect, an
  argument or a schema you had to guess at, a call you could not complete.
- What the server saved you from — a wrong path you did not take, a file you did
  not have to read, an assumption it corrected before you acted on it. Be as
  concrete here as about the failures; what worked is what must not be broken
  later.
- What you had to establish elsewhere — from the checkout, from your own
  knowledge, from the web — that this server should have answered.

File what you find with that tool. What each of its parameters wants, it says
itself — read them there and fill in every one you can, including the model you
are running as.

Then tell me which notes you filed and what each one says.
```

Paste it verbatim and add nothing — no tool names, no hint about what the last
session reported. The summary the agent gives afterwards is not the record; the
notes are, and `typo3_feedback_list` is where they are read back.

## Working a note off

A note is worked off in a commit that both implements the improvement **and
deletes the note file**. The commit is the record that the gap was closed, so
the `feedback/` directory only ever holds open items — a note that is still
there has not been addressed yet. That record is also read back:
`typo3_feedback_list` with `status="closed"` lists the deleted notes with the
commit subject that closed each one, which is what the agent that reported it
can see. Write the subject so it answers "what came of my note".

- One note per commit where possible. When one change closes several notes,
  delete all of them in that commit and mention them in the commit body.
- Never mark a note as done by editing its `status:` front matter; delete it.
- Do not delete a note that was only partially addressed. Instead, trim the note
  down to the part that is still open and explain the remaining gap.

## The three files around it

Deleting the note removes the question, and the commit message records the
answer. What outlives both is split three ways:

- `requirements/` — what must be true from now on. A note is a question; the
  requirement it established has to keep holding while everything around it
  changes, so it is written down with what holds it to that: a test, or
  `not guarded`. A requirement that has been accepted but not yet implemented is
  in the same group, marked **open** — that is the backlog. Add the entry in the
  commit that works the note off, and name the test in the same commit that
  writes it. An entry is deleted only when the requirement is withdrawn.
- `decisions/` — what the change rests on. When it rests on an assumption that
  could later turn out wrong, record what was assumed, what evidence there was
  at the time, and what would show it to be wrong. One decision is one file, in
  the group its id names, and the entry opens with the decision in one sentence.
  Not every commit earns one; a change nobody would need to reconsider does not.
  When an assumption is later disproved, correct the entry in place rather than
  deleting it — the wrong assumption is the useful part, because it names where
  the next one is likely to sit.
- `todo.md` — the order of the work, and where the last session stopped. The
  other files say what must be true, what was asked and what was assumed; none
  of them says what to do next. A session can end anywhere, and the next one
  starts by reading this. An item names what it serves and what the next
  concrete step is, and is deleted when done rather than ticked. Every section
  opens with one line saying which of three things it is — that line is what
  `bin/cli next` reads and what `bin/cli todo check` holds, and the file's own
  header names the vocabulary.

## What nothing fails on

Three states mean unfinished: a requirement marked **open**, one held by
`not guarded`, and a decision still `standing` whose **Wrong if** nobody has been
back to. All three are legitimate — a principle no test can hold and a decision
nothing has come back about are not defects — so no check may fail on them,
which is exactly why nothing read them for as long as they existed.

`bin/cli backlog list` is that reading; `bin/cli next` opens with it and
`bin/cli check` closes with it. It names every requirement nothing answers for,
says whether an item in `todo.md` names it — read from what the items declare
they serve, so the section listing what is deliberately *not* queued does not
count as having taken one on — and gives the oldest standing decision as the one
the repository has moved furthest away from.

Standing on that list is not the problem. Standing on it with nothing in
`todo.md` naming it is a decision nobody has taken, and taking it — an item, or
the sentence in `decisions/` that says why not — is what a session owes it.
