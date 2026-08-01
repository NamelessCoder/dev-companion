---
id: D-FBK-7
date: 2026-08-01
status: standing
---

# D-FBK-7 — How a todo is worked travels with the todo

**The reading and the research a todo is owed are written down as a procedure
page, and `bin/cli next` prints the pointer to it with every todo it hands over
rather than leaving it to be looked up.**

Everything about the order of the work was written down and nothing about doing
one piece of it was. A session that gets one todo, printed as an imperative
paragraph, has every incentive to start at the first sentence of the step — and
the two things that decide whether the change is right happen before it: reading
what the todo serves against what the code does today, and settling a question
from the checkouts, the manuals or this server's own tools instead of from
recall.

- **Evidence:** the queue on the day it was written — 35 sections, nearly all of
  them serving a decision, and most in the shape "the entry names the failure;
  read whether it happened". Every one of those is a research task whose output
  is either a **Tested on** line or a test, and none of them says so; the
  authoring page for skills had just gained the same two steps (`R-SKL-6`,
  2026-08-01) after a skill was written from recall, which is the same failure
  one directory over. What `next` printed was the todo, the run output and one
  line about deleting it afterwards.
- **Decided:** one page, [documentation/working-a-todo.md](../../documentation/working-a-todo.md),
  covering what is read first, that the step is judged rather than executed,
  that a question the work turns on is settled from a source or recorded as
  open, that what has no source here is asked before the change, and what the
  file has to say afterwards — plus one closing block on `bin/cli next` naming
  it. `Todo::PROCEDURE` holds the path so the pointer and the page cannot drift
  apart, and `R-FBK-9` carries the demand. The handover half stayed in the
  command rather than moving to the page with everything else: which of the
  three cases applies is read off the todo, and the page cannot know which one
  it is being read for.
- **Decided:** that putting the todo back is offered with the question rather
  than kept as a fallback, and that a todo put back goes to the end of the
  queue. The person asked can be out of answers too, and a session that has only
  "decide it" on the table will decide it — so the todo keeps its section, gains
  the question in the words it was asked in and what the reading already
  established, and moves last. Last rather than down, because `next` hands over
  the first queued item and has no notion of blocked: one left in place is
  handed to every session after this one, which is the queue not moving at all.
  Nothing new holds either half — it is the existing rule that a change of order
  is written down before the work, applied to a todo that never started.
- **Assumed:** that a pointer handed over with the work is read where a page in
  `documentation/` is not. That is the whole bet: the page has existed for one
  commit and no session has been observed reading it. The alternative — putting
  the reading itself into the output — was rejected because `next` exists to
  print one todo and nothing else (`D-FBK-3`), and a command that grows a second
  paragraph of instruction every time something is forgotten ends as the 62-line
  output that decision cut.
- **Assumed:** that the research a todo needs is worth naming by source at all.
  Most sessions here run without an installation and with the checkouts already
  present, so the sources are few and stable; if that stops being true the list
  becomes a menu nobody reads to the end.
- **Assumed:** that asking is cheap where it is right, which holds while the
  person who queues the todos is the one the session is talking to. A session
  running unattended — a scheduled run, a forward review in somebody else's
  agent — has nobody to ask, and for it the instruction degrades to recording
  the open question instead, which is the same three places research that ran
  out already uses.
- **Wrong if:** a commit lands that states a version-bound fact, a default of a
  tool this repository does not own, or the current state of a note, with no
  reading behind it — the page is then present and inert, and what is left is
  putting the question into the todo itself rather than into a page it points
  at. Or the closing lines start being echoed back by sessions as a plan
  ("first I will read what it serves") without any file being opened, which is
  the failure mode of every instruction handed to an agent and is readable in a
  transcript. Or the queue stops being answerable this way at all, because the
  research a todo needs turns out to need an installation more often than not —
  then the page is naming sources that are not there.
