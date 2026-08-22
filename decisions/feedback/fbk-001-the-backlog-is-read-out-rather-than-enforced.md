---
id: D-FBK-001
title: The backlog is read out rather than enforced
date: 2026-07-31
status: confirmed
---

# D-FBK-001 — The backlog is read out rather than enforced

**What `requirements/` and `decisions/` say is unfinished is reported and never
fails a check; the coupling to the queue is a line of output, not a rule.**

Both directories carry a state that means unfinished, and until today nothing
read either of them. The question was whether making them visible is enough, or
whether the check has to fail until an entry is queued.

## Evidence

- What the first reading found, on the day it was written. - 105 requirements,
  of which one is `open` and one is `not guarded`. `R-AUD-002` has been open
  since the directory was created on 2026-07-29, has a contract case in
  `META-03` that names the unmet half outright, and no item in `todo.md` had
  ever named it. Nothing had gone wrong; nobody had looked. - 31 decisions, of
  which 29 are `standing`. Every one of them wrote down a **Wrong if**, and one
  entry has ever been back-checked. - the queue is fed by `feedback/` and the
  forward reviews and by nothing else, so neither directory had a path into the
  order of the work at all.

## Decided

- `bin/cli unresolved:list` reads both out, `bin/cli repository:check` closes
  with the same block, and the exit code is untouched. Three stricter shapes
  were rejected with it: failing the check while an `open` requirement has no
  item, requiring `not guarded` to carry a reason, and giving `standing` an
  expiry. All three turn a legitimate state into an error — a principle no test
  can hold and a decision nothing has come back about are not defects — and the
  third would have made CI red on 29 entries the day it landed, which is how a
  check gets switched off rather than answered.

## Assumed

- That a session which reads the line acts on it. The item asks for the
  judgement rather than the work, so acting can be one sentence saying an entry
  stays as it is — a much lower bar than fixing it, and the reason the soft form
  was thought sufficient.
- That the 29 standing decisions are mostly standing because they are still
  true, so a count and the oldest is a fair summary. If a third of them turn out
  to have been overtaken, the summary was hiding a queue.

## Wrong if

- The same id is still reported with `no todo names it` after three sessions
  that ran the check, or the standing count only ever grows and no session sorts
  them. Then visibility was not the missing part, the coupling has to become a
  rule, and the shape to reach for is the one rejected here:
  `bin/cli requirements:check` fails while an `open` entry is unqueued.

## Since then

The command was renamed. What this entry calls the backlog is read out by
`bin/cli unresolved:list` from 2026-08-04, and the word itself was retired
because it named three different sets at once —
[`D-FBK-041`](fbk-041-what-nothing-answers-for-is-called-unresolved.md). What
was decided here is untouched: the reading still reports and still fails
nothing.

## Confirmed on 2026-08-02

Neither half of the **Wrong if**. No id is reported with `no todo names it` at
all — `bin/cli unresolved:list` says every requirement is met and guarded — so
the first half currently has no subject to repeat, which is a weaker result than
it looks: it says the requirement side is clean, not that a session acts on the
line when it is not. The second half is the one with movement. The standing
count fell from 42 to 38 of 52 that day before this entry's own status changed
it to 37, across four back-checks — three from sessions run in parallel, the
fourth from the session that merged them — so it does not only ever grow and the
sorting is happening. Those four also speak to the second **Assumed**: all four
came back `tested` rather than `corrected`, so the entries were standing because
they were still true, and the summary was not hiding a queue. Four is a day
rather than a trend, and the oldest standing entry is still older than this
reading.
