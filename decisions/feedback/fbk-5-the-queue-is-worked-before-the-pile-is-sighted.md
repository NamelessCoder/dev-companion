---
id: D-FBK-5
date: 2026-08-01
status: standing
---

# D-FBK-5 — The queue is worked before the pile is sighted

**`bin/cli next` asks what has a clock, then the queue, and only with the queue
empty what recurs every session — and that sighting hands over five notes at a
time rather than the directory.**

What recurs every session is sighting: reading `feedback/` and the backlog and
deciding what of it becomes work. It was asked first, on the reasoning that a
session should know what arrived before it starts. That holds while the pile is
small, and `feedback/` is the one directory in this repository that fills from
outside it.

- **Evidence:** 56 open notes on the day it was changed, 55 named by no todo,
  against 38 items in the queue. `bin/cli feedback list` exited nonzero on the
  first of those 55, so the sighting was due in every session, printed 57 lines
  of filenames before its own paragraph, and no queued item had been reached by
  `next` since the queue was written. Two of the three recurring todos are
  sightings; 20 of the 56 notes were duplicates of two sessions.
- **Decided:** three groups in a fixed order. A cadence in days is an
  appointment and keeps its place at the front — missing it is missing a day.
  The queue is next, because judging a note is what puts an item into it: a
  queue with entries is a queue of decisions already taken, and sighting more
  instead is deciding twice and doing nothing. The sightings come last, when
  the queue is empty and their whole output — new entries — is what is needed.
  The portion is five, so that the judgements can be read and disagreed with by
  somebody who is not the session that made them; the listing names each note's
  category, model and first line for the same reason. `bin/cli feedback list`
  stays the whole of it, and reads rather than works.
- **Assumed:** that the queue empties. It is the mirror image of what this
  corrects, and nothing has run long enough to show it: a queue that never runs
  dry starves the notes exactly as the notes starved the queue, and the
  directory grows from every session everywhere either way.
- **Assumed:** that oldest-first is the right end to take five from. A note that
  has waited while fresher ones arrived in front of it is the one at risk of
  never being read, but the newest note is the one about the server as it is
  now — and evidence about a version that no longer exists is what re-running
  the query is for.
- **Wrong if:** `feedback/` keeps growing while the queue never empties, so the
  five notes are never handed over at all — then the sighting needs a place of
  its own rather than a position behind the queue, and the answer is probably a
  cadence in days like every other appointment. Or the five come round but the
  judgements are not read by anybody, in which case the portion was cut for a
  reader who does not exist and the number can be whatever a session can carry.
