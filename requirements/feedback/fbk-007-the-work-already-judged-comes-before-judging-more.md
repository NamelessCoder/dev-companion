---
id: R-FBK-007
status: held
restsOn: [D-FBK-012, D-FBK-016]
---

# R-FBK-007 — The work already judged comes before judging more

**A todo somebody has given a priority is handed over before one nobody has
judged, and what is handed over at all is one of them.**

Judging a feedback is what turns it into work, so anything carrying a priority
is a decision already taken. Reaching for an unjudged one while those wait is
deciding twice and doing nothing — and `feedback/` fills from every session
everywhere while one session judges one, so whatever is asked first is asked in
every session there will ever be.

What used to carry this was an order between three groups: the queue before the
sightings, and a sighting reached only once the queue was empty. It is carried
now by the priority itself, because the feedback are on the board rather than
behind it. A card written for a feedback has no priority until somebody decides
what the feedback is worth, and having none sorts it below all three words —
the same place the sighting left it, without a second mechanism to say so.

The size of the reading is the same requirement from the other end. What is
handed over has to stay the size of a session's work while the directory grows
without limit, and one card is one card however many are on the board. That the
judgements can be read by somebody who was not the session that made them is no
longer what the portion carries: a judgement is written into the decision it was
made against, or into a new one where nothing says it yet, so the record
outlives the run that made it.

## From

56 open feedback against 38 queued items on 2026-08-01, of which the queue had
been reached in no session since it was written; the sighting printed 57 lines
before its own instruction. See
[D-FBK-012](../../decisions/feedback/fbk-012-the-queue-comes-first-and-the-sighting-hands-over-one.md),
which carries both halves of this and names
[D-FBK-005](../../decisions/feedback/fbk-005-the-queue-is-worked-before-the-pile-is-sighted.md)
as the entry the measurement was recorded in. The order became a priority on
2026-08-02, with 67 feedback and an empty queue —
[D-FBK-016](../../decisions/feedback/fbk-016-a-feedback-waits-on-the-board-rather-than-behind-it.md).

## Held by

- `TodoTest::theQueueIsReadByPriorityAndThenByAge`
- `TodoTest::everyOpenFeedbackIsOnTheBoard`
