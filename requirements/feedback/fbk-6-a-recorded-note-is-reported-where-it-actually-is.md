---
id: R-FBK-6
status: held
---

# R-FBK-6 — A recorded note is reported where it actually is

**The path a recorded note is reported back with is absolute, and the answer
says the note was written into this server's own checkout rather than into the
project the session is working in.**

The caller stands somewhere else. A path relative to a root it has never seen
resolves, in the only directory it can check, to nothing — and a write that
cannot be verified is read as a write that failed, by an agent doing exactly
what it should. The file is there the whole time, one checkout over, and saying
which checkout is the difference between a note that was recorded and a note
that was recorded twice.

**From:** the note of 2026-07-31 17:23, recorded from a site package: the tool
answered `feedback/<name>.md`, the session searched its own workspace for that
path, found neither the file nor a `feedback/` directory, and reported the
creation as failed.

**Held by:** `FeedbackTest::theRecordedNoteIsReportedWhereItActuallyIs`
