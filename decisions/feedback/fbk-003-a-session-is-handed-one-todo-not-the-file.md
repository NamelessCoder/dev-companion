---
id: D-FBK-003
title: A session is handed one todo, not the file
date: 2026-07-31
status: revoked
revokedBy: D-FBK-002
---

# D-FBK-003 — A session is handed one todo, not the file

**`bin/cli todo:next` prints the first todo that is due and nothing else.**

The overview it used to print is `bin/cli todo:list`, and being due is answered
by a cadence and by the todo's own command exiting nonzero.

The command was built to spare a session the reading of four files, and it
replaced them with one reading of its own that was nearly as long. What it costs
to hand an agent context it did not ask for is not zero, and the file it read
from had grown to match: items that carried three moves each, with the move
behind the paragraph explaining why the order is what it is.

## Evidence

- The output on the day it was changed — 62 lines, of which the one item at the
  front was 396 words. Two of that item's three paragraphs were history (why it
  moved ahead on 2026-07-31, what the second run of that day produced), and the
  first imperative sentence was in the third. It also said "name it as `E-EXT`
  **above**", pointing at a section marked `**Not an item.**` that `next` does
  not print, so the instruction resolved to nothing for its only intended
  reader. Of the three standing sections, two were performed and one printed a
  command to run, and nothing in the output distinguished them. The listings
  both exited 0 whatever they found. What replaced it is 19 lines and 139 words.

## Decided

- One todo, whole, with its `Run:` command already executed. Due is the cadence
  — `session`, or a number of days with a `Checked:` date, so five sessions in
  an afternoon do not ask five times whether the SDK has released — and then the
  command's exit code, which is what lets the feedback stop being the next thing
  the moment the last one is judged without anybody editing the file. With that,
  a todo that recurs is a todo with a cadence rather than a kind of section, so
  `**Standing:** feedback | backlog | by hand` became fields any todo can carry
  and the three special cases in the dispatcher went with it. One paragraph is
  one step, and the six items that carried more became ten.

## Assumed

- That the judgement, not the work, is the right threshold for "there is
  something here". A feedback some todo already names is being worked off in the
  order the queue has it, and stopping to re-read it is how the queue never gets
  reached. Nothing has run long enough to show that the ones already named stay
  named.
- That a session which never sees the queue does not need it to work in the
  right order — the order is the file's, and it is the file that decides which
  todo is printed. What this gives up is the reader who would have noticed that
  the order is wrong, and `bin/cli todo:list` is where that reader has to go on
  purpose now.

## Wrong if

- Sessions start asking for context `next` withheld, or open by running
  `todo list` anyway — then one todo is less than the minimum and the cut was in
  the wrong place. Or a recurring todo blocks the queue for more than a session
  or two, which would mean its command answers "there is work" to a state nobody
  can finish. Or the todos grow back into packages, because splitting them is a
  habit and nothing checks it: the paragraph is prose by design, and no check
  can tell one step from three.

## Since then

The first **Wrong if** has been measured and did not happen, on the three
sessions run in parallel on 2026-08-02. It is not a **Tested on** because the
entry already carries a **Corrected on**, and the status names one line only —
what a second **Wrong if** coming back clean is called is a gap in that
vocabulary, not a claim about this one. Each session opened with the check it
was asked for and then called `bin/cli todo:next --worktree` exactly once;
across 146 shell calls between them, `bin/cli todo:list` was run zero times.
None asked for context beyond the todo, and none recorded a `**Waiting on:**`,
so no session was blocked on something the one todo withheld. Read from the
session transcripts rather than from what the sessions reported about
themselves, which is what the caller-chosen session id in
[driving-a-session.md](../../documentation/contributing/driving-a-session.rst)
is for. Two things it does not settle: all three were handed a message naming
`todo:next --worktree` as where the work is, so they were steered to the command
rather than choosing it, and a worktree session is not the plain
`bin/cli todo:next` case this entry was written for. The third **Wrong if** was
measured the same day under
[`D-FBK-002`](fbk-002-the-order-of-the-work-is-declared-not-inferred.md): the
todos have not grown back into packages, and one of these sessions split a
second step out into its own todo rather than doing it.

## Revoked on 2026-08-01

The second **Wrong if** happened, in the form the first **Assumed** described. A
recurring todo blocked the queue — not for a session or two but for every
session there was, and the feedback it sighted were what nobody could finish: 56
open, 55 of them named by no todo, against a queue of 38 items that `next` never
reached. The threshold was right and the order was wrong. Judging a feedback is
what puts an item *into* the queue, so asking the sighting first means deciding
twice and doing nothing, and the directory it decides over grows from every
session everywhere while one session judges a handful. `next` now asks in three
groups — what has a clock, then the queue, then the sightings once the queue is
empty — and the sighting hands over five rather than the directory, because five
judgements are the number somebody can disagree with before the commit is made.
