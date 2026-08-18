---
id: D-FBK-012
date: 2026-08-02
status: open
---

# D-FBK-012 — The queue comes first, and the sighting hands over one

**`bin/cli todo:next` asks what has a clock, then the queue, then what recurs
every session — and that sighting hands over one feedback.**

The sightings are reached only with the queue empty, and `bin/cli feedback:next`
hands over the oldest one no todo has judged.

This is what survived `D-FBK-005` and what replaced the rest of it. The order is
that entry's, untouched, and the evidence below is the evidence that bought it.
The portion is not. Five was cut for a reader who was then sent to look for the
judgements in a commit, and what replaces it is where a judgement is written
rather than how many are made at once.

## Evidence

- 56 open feedback against 38 queued items on 2026-08-01, 55 of them named by no
  todo. `bin/cli feedback:list` exited nonzero on the first of those 55, so the
  sighting was due in every session, printed 57 lines of filenames before its
  own paragraph, and no queued item had been reached by `next` since the queue
  was written. Two of the three recurring todos are sightings. Measured and
  recorded in
  [`D-FBK-005`](fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md),
  which is where it is still read.
- The second **Wrong if** of that entry, on 2026-08-02. The five came round and
  the judgements were read by nobody, because the only place they were written
  was the commit that made them — which is the one place nothing searches. The
  portion had been cut for a reader who could not have existed.

## Decided

- Three groups in a fixed order. A cadence in days is an appointment and keeps
  its place at the front, because missing it is missing a day. The queue is
  next, because judging a feedback is what puts an item into it: a queue with
  entries is a queue of decisions already taken, and sighting more instead is
  deciding twice and doing nothing. The sightings come last, when the queue is
  empty and their whole output is what is needed.
- One feedback rather than a handful. `bin/cli feedback:next` hands over the
  oldest one no todo has judged and exits nonzero while any remain, so a run is
  one judgement and the loop ends when nothing is unjudged rather than after a
  fixed number. It prints the category, the model and the first line, because a
  filename alone cannot be disagreed with.
- The judgement is written into `decisions/`. The entry it was made against is
  updated, and where the judgement establishes something no entry says yet, a
  new one is created — so the reading survives the run instead of being a window
  onto five files. There is no journal beside the archive: a second list of the
  same judgements is a second thing to keep true.
- `bin/cli feedback:list` stays the whole of it, newest first, and reads rather
  than works.

## Assumed

- That the queue empties. Carried over from `D-FBK-005` unmeasured, and it is
  the mirror image of what the order corrects: a queue that never runs dry
  starves the feedback exactly as the feedback starved the queue.
- That oldest-first is the right end to take one from. A feedback that waited
  while fresher ones arrived in front of it is the one at risk of never being
  read, and the newest is the one about the server as it is now — which is what
  re-running the query is for.

## Wrong if

- `feedback/` keeps growing while the queue never empties, so the one is never
  handed over at all. Then the sighting needs a place of its own rather than a
  position behind the queue, most likely a cadence in days like every other
  appointment.
- The judgements stop arriving in `decisions/`. A run that judges a feedback and
  writes nothing anywhere is the failure five was cut against, reached by the
  other road: nothing fails on an entry nobody wrote, and the only trace is a
  feedback marked judged by a todo that says less than the judgement did.
- What stands between feedback goes unsaid. One feedback correcting three
  earlier ones, or the same gap reported by four sessions, is what a portion of
  several could see and one cannot. `decisions/` is where that has to become
  visible now, and a directory that only ever restates single feedback would
  mean it has not.

## Covered by

- `CliTest::theSightingsWaitForAnEmptyQueue`
- `CliTest::whatRecursIsEitherAnAppointmentOrASighting`

## Since then

The second half of the statement is gone with the command it names.
`bin/cli feedback:next` and the feedback sighting that ran it were deleted on
2026-08-02 by
[`D-FBK-016`](fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md),
which gave every open feedback a card in the queue instead, and what writes that
card became `typo3_feedback_record` itself on 2026-08-14 —
[`D-FBK-045`](fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md). So a
feedback is no longer reached through a sighting at all: it is queued when it
arrives and judged in the order the queue has. The first **Wrong if** was
answered by making it moot rather than by being measured.

The first half stands and is what this entry is still read for.
`bin/cli todo:next` asks the three groups in that order, an appointment is due
on a cadence in days and on its own command exiting nonzero, and the sightings
are reached with the queue empty — one of them is left, and it runs
`bin/cli unresolved:list`.
[`D-EVI-007`](../evidence/evi-007-a-case-no-test-holds-says-so-with-its-exit-code.md)
rests on that exit code, and what it cost to have a command answer 0 while work
stood is written up there.
