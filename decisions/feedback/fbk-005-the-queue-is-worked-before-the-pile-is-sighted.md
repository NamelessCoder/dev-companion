---
id: D-FBK-005
date: 2026-08-01
status: revoked
---

# D-FBK-005 — The queue is worked before the pile is sighted

**`bin/cli todo:next` asks what has a clock, then the queue, and only with the queue
empty what recurs every session.**

That sighting hands over five feedback at a time rather than the directory.

What recurs every session is sighting: reading `feedback/` and the backlog and
deciding what of it becomes work. It was asked first, on the reasoning that a
session should know what arrived before it starts. That holds while the pile is
small, and `feedback/` is the one directory in this repository that fills from
outside it.

## Evidence

- 56 open feedback on the day it was changed, 55 named by no todo, against 38
  items in the queue. `bin/cli feedback:list` exited nonzero on the first of
  those 55, so the sighting was due in every session, printed 57 lines of
  filenames before its own paragraph, and no queued item had been reached by
  `next` since the queue was written. Two of the three recurring todos are
  sightings; 20 of the 56 were duplicates of two sessions.

## Decided

- Three groups in a fixed order. A cadence in days is an appointment and keeps
  its place at the front — missing it is missing a day. The queue is next,
  because judging a feedback is what puts an item into it: a queue with entries
  is a queue of decisions already taken, and sighting more instead is deciding
  twice and doing nothing. The sightings come last, when the queue is empty and
  their whole output — new entries — is what is needed. The portion is five, so
  that the judgements can be read and disagreed with by somebody who is not the
  session that made them; the listing names each feedback's category, model and
  first line for the same reason. `bin/cli feedback:list` stays the whole of
  it, and reads rather than works.

## Assumed

- That the queue empties. It is the mirror image of what this corrects, and
  nothing has run long enough to show it: a queue that never runs dry starves
  the feedback exactly as the feedback starved the queue, and the directory
  grows from every session everywhere either way.
- That oldest-first is the right end to take five from. A feedback that has
  waited while fresher ones arrived in front of it is the one at risk of never
  being read, but the newest feedback is the one about the server as it is now
  — and evidence about a version that no longer exists is what re-running the
  query is for.

## Wrong if

- `feedback/` keeps growing while the queue never empties, so the five are
  never handed over at all — then the sighting needs a place of its own rather
  than a position behind the queue, and the answer is probably a cadence in
  days like every other appointment. Or the five come round but the judgements
  are not read by anybody, in which case the portion was cut for a reader who
  does not exist and the number can be whatever a session can carry.

## Revoked on 2026-08-02

The portion is one, not five. The order this entry is mostly about —
appointments, then the queue, then the sightings — is untouched and is what the
evidence above bought; what was wrong is the number beside it. Five was cut for
a reader: somebody who did not make the judgements should be able to read them
together and disagree before the commit. That reader was being asked to find
them in a commit, which is the one place a judgement is not searchable, and the
second **Wrong if** named exactly this outcome. What replaces the portion is
where the judgement is written rather than how many are made at once: the
decision it was judged against is updated, and where the judgement establishes
something no entry says yet, a new one is created. So the reading survives the
run instead of being a window onto five files, and `bin/cli feedback:next`
hands over the oldest unjudged one and exits nonzero while any remain. What
this gives up is the run that sees two feedback at once — one correcting three
earlier ones, or the same gap reported by several sessions — and `decisions/`
is where that has to become visible now.
