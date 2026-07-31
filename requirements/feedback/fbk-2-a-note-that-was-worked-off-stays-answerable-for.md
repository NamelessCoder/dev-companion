---
id: R-FBK-2
status: held
---

# R-FBK-2 — A note that was worked off stays answerable for

**A note that was worked off stays answerable for.**

Closing one means deleting its file, and the agent that recorded it sees only
that the file is gone — which reads as lost, so the same gap is reported
again and a request that needed a code change is dropped silently. The commit
that deleted it is the record of what came of it, and `typo3_feedback_list`
reads it back rather than the store keeping a second copy of what git already
has.

**From:** seventeen notes recorded over two sessions, of which the store showed
three, and a re-report of a request that had shipped in the meantime
(2026-07-29).

**Held by:** `FeedbackTest::aNoteThatWasWorkedOffIsStillAnswerableFor`
