# A feedback field that was cut says so

**Serves:** feedback/
**Priority:** normal
**Branch:** todo/a-feedback-field-that-was-cut-says-so
**Claimed:** 2026-08-03

Found on 2026-08-03 while judging `feedback/2026-08-03-144316`, whose
observation is exactly 4000 characters and ends `the skill fixed the or`. The
cut is `Channel::MAX_FIELD_LENGTH` in `Channel::text()`, it lands mid-word, and
nothing says it happened — not the file, not the answer
`typo3_feedback_record` returns. What it took there was the sentence naming the
shape the session was reporting, which is the half a strength is judged on.
`D-FBK-018` has the case. One field in 235 recorded feedback sits on the cap, so
this is rare rather than widespread; it is `normal` because a silent cut cannot
be noticed from the file and the marker costs a line.

Make `Channel::text()` mark its cut the way the file already marks its other
two. `title()` three lines below ends a shortened heading in `...`, and a
redacted value stands in the file as `[redacted: ...]` with the answer naming
what went — on the stated ground that a report which was altered has to say so.
Write the marker into the stored field and name the cut in the `ToolResult`
beside `redacted`. Assert both in `FeedbackTest`, for a field longer than the
cap and for one exactly on it, which is the case that must not be marked.
