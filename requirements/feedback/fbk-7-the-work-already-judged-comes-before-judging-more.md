---
id: R-FBK-7
status: held
---

# R-FBK-7 — The work already judged comes before judging more

**`bin/cli next` reaches the queue before anything that recurs every session,
and hands over at most five notes when it does reach the sighting.**

Sighting the notes and the backlog is what turns them into queued items, so a
queue that still has entries is a queue of decisions already taken. A sighting
is due for as long as anything is unjudged, and `feedback/` fills from every
session everywhere while one session judges a handful — asked first, it is due
in every session there will ever be, and the queue behind it is never reached.

The size of the reading is the same requirement from the other end. What is
handed over has to stay the size of a session's work while the directory grows
without limit, and it has to stay small enough that the judgements can be read
by somebody who was not the session that made them.

**From:** 56 open notes against 38 queued items on 2026-08-01, of which the
queue had been reached in no session since it was written; the sighting printed
57 lines before its own instruction. See
[D-FBK-5](../../decisions/feedback/fbk-5-the-queue-is-worked-before-the-pile-is-sighted.md).

**Held by:** `CliTest::theSightingsWaitForAnEmptyQueue`,
`CliTest::whatRecursIsEitherAnAppointmentOrASighting`
