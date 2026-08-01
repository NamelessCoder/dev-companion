# What is being worked on

This directory exists so a session can end anywhere. It holds the **order of the
work** and where the last one stopped — not what must be true (that is
[requirements/](../requirements/readme.md)), not the questions real sessions
asked (that is `feedback/`), not the map of what the audiences need (that is
`scenarios/`). Those three outlive the work; this one is consumed by it.

Nobody reads it to start. `bin/cli todo:next` prints the one todo that is due and
nothing else, `bin/cli todo:list` is the overview, and this is where both of
them read from.

One todo is one file, and where it sits says what it is:

- **`NNN-<title>.md`** — the queue, and the number is the place in it. A todo
  that moves is renamed, one that is finished is deleted, and a new one is a new
  file, so two sessions can both add work without writing the same file. The
  numbers run in tens, which is what leaves room to put one between two others.
- **`recurring/`** — what comes round and is never deleted.
- **`progress/`** — what a session has in hand. The queue is an order, not an
  assignment, so `bin/cli todo:next` hands the same first item to everybody who
  asks; where two sessions work at once, taking one on is a move out of the
  queue, and it is offered to nobody else from then on. It says on which branch
  the work is and since when, because a claim nobody came back to has to be
  readable as one. Finishing it is still a deletion, and the answer to a
  question it carries is what puts it back.
- **`waiting/`** — what no session can start, because it is blocked on an
  answer nothing here can produce. It says what it waits on, `bin/cli todo:next`
  offers it to nobody, and the answer is what numbers it back into the queue.
  A recurring todo asks every seven days what is in here, so a question waits
  rather than disappears.
- **`reference/`** — none of the four: what a session would otherwise
  rediscover and mistake for work.

Each file opens with its title, then a head of labelled lines:

- `**Serves:** <ids>` — what this answers for: a requirement, a feedback, a
  scenario, a directory. Without it, it is an idea rather than a todo, and
  ideas go in the feedback that had them.
- `**Every:** session` or `**Every:** 7 days` — the cadence of a recurring todo.
  A cadence in days is an appointment and comes before the queue; `session` is
  a sighting and comes after it, when the queue is empty.
- `**Checked:** <date>` — when a todo measured in days last ran. The session
  that runs it writes the date.
- `**Run:** <command>` — where the step starts. `bin/cli todo:next` runs the ones
  this repository owns and names the rest.
- `**Waiting on:** <the question>` — what a todo is blocked on, in the words it
  was asked in, wrapped onto indented lines where it is longer than one.
  `bin/cli todo:waiting` is what asks it again. In `waiting/` it is the whole of
  the file; in `progress/` it is a question that arrived mid-work, and the
  branch is what still holds the half that is done.
- `**Branch:** <branch>` — where the work on a todo in `progress/` is. A claim
  whose half-finished diff cannot be found is worth less than no claim.
- `**Claimed:** <date>` — when it was taken on. A state that locks everybody
  else out of one todo has to be readable as stale.

Then one paragraph, and one only: the **next concrete step**, in enough detail
that someone who has read nothing else can start. "Continue with the bindings"
is not that; "bind the statements in `php.json` against `.checkouts/12.4` and
`13.4`" is. Two steps are two todos — a paragraph is printed whole, and a
session that reads three of them to find where to start is reading instead of
working.

That is the form. What is due and in which order:
[documentation/feedback/readme.md](../documentation/feedback/readme.md). What a
finished, a half done or a put back todo leaves here:
[documentation/feedback/working-a-todo.md](../documentation/feedback/working-a-todo.md).
