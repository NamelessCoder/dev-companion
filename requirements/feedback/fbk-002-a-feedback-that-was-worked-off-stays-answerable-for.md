---
id: R-FBK-002
title: 'A feedback that was worked off stays answerable for'
status: held
---

# R-FBK-002 — A feedback that was worked off stays answerable for

**A feedback that was worked off is kept, and says what came of it.**

Closing one means moving it to `feedback/archive/`, not deleting it. The agent
that recorded a deleted feedback saw only that the file was gone — which reads
as lost, so the same gap is reported again and a request that needed a code
change is dropped silently. `typo3_feedback_list` reads the archived feedback
back whole, with the commit that archived it as the answer; the feedback worked
off before the archive existed carry that commit in their own front matter,
because they were all moved in one commit that says nothing about any of them.

Keeping the file is also what makes the closed half filterable at all: a
feedback read out of a commit was a filename, and the category, the tools and
the model it carried went with the file.

## From

Seventeen feedback recorded over two sessions, of which the store showed three,
and a re-report of a request that had shipped in the meantime (2026-07-29).
Reading the commit back was that answer; what it could not give back was
everything the feedback itself said (2026-08-01).

## Held by

- `FeedbackTest::aNoteThatWasWorkedOffIsStillAnswerableFor`
- `FeedbackTest::aNoteThatWasWorkedOffKeepsEverythingItSaid`
