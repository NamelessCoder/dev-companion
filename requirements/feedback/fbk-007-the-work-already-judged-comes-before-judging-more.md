---
id: R-FBK-007
status: held
restsOn: [D-FBK-005]
---

# R-FBK-007 — The work already judged comes before judging more

**`bin/cli todo:next` reaches the queue before anything that recurs every session,
and hands over one feedback when it does reach the sighting.**

Sighting the feedback and the backlog is what turns them into queued items, so a
queue that still has entries is a queue of decisions already taken. A sighting
is due for as long as anything is unjudged, and `feedback/` fills from every
session everywhere while one session judges a handful — asked first, it is due
in every session there will ever be, and the queue behind it is never reached.

The size of the reading is the same requirement from the other end. What is
handed over has to stay the size of a session's work while the directory grows
without limit. That the judgements can be read by somebody who was not the
session that made them is no longer what the portion carries: a judgement is
written into the decision it was made against, or into a new one where nothing
says it yet, so the record outlives the run that made it.

## From

56 open feedback against 38 queued items on 2026-08-01, of which the queue had
been reached in no session since it was written; the sighting printed 57 lines
before its own instruction. See
[D-FBK-005](../../decisions/feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md).

## Held by

- `CliTest::theSightingsWaitForAnEmptyQueue`
- `CliTest::whatRecursIsEitherAnAppointmentOrASighting`
