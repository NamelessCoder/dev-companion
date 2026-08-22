---
id: D-FBK-049
title: A stored field states the cap it is cut at
date: 2026-08-18
status: open
---

# D-FBK-049 — A stored field states the cap it is cut at

**Every field `typo3_feedback_record` shortens states its limit in its own
description, and the answer names the subject the way it names the other
three.**

`R-FBK-015` made a cut say so after it happened, and that half works: the
session that reported this one read `cut` and refiled what it had lost. What
nothing says is the budget before a word is written, so a caller writes to the
brief and finds out afterwards which sentences it cost.

## Evidence

- `feedback/2026-08-18-080803`, from `/home/benji/projects/blog`. It reports
  losing the last 650 characters of `feedback/2026-08-18-080710`, and that file
  carries `[cut: 650 characters past the 4000-character limit]` where its
  observation stops. The loss is exactly what the report says it is.
- `Channel::MAX_FIELD_LENGTH` is 4000 and `text()` applies it to `observation`,
  `query` and `suggestion`. `model` has its own 80. `subject` goes through
  neither: `title()` cuts the heading it becomes to 97 characters plus `...`,
  and the subject is stored nowhere else, so what stood past 97 is gone. None of
  the four descriptions in `FeedbackRecord::inputSchema()` states a number.
- The cap the feedback reports is the rare one. Across 440 recorded feedback,
  open and archived, two carry a `[cut:` marker.
- The cap it did not know about is the common one. 321 of those 440 have a title
  ending in `...`, and for 115 of them the title's 97-character stem is not how
  the observation opens — so a subject was given and shortened. Neither the file
  nor the answer says a word about any of them.
- The feedback under judgement is one of the 115. Its title ends
  `so it is only discovere...` and its observation opens `Task: fix ext:blog's`.
  It lost characters from two fields, reported one, and never saw the other.
- `bin/cli hints:probe` on the feedback's own query reaches nothing, and would
  not: what is missing is a sentence in a schema this repository owns rather
  than a statement about TYPO3. Rung 1a does not apply.
- `R-FBK-015` holds, and holds for what `text()` handles.
  `FeedbackTest::aFieldCutForLengthSaysSoInTheFileAndInTheAnswer` and
  `aFieldExactlyOnTheCapIsNotMarked` are on that path. The subject is outside
  both.

## Decided

- Step 4, wording. The rule is not missing and nothing has to move — the number
  exists, in the one place a caller cannot read. It changes a tool's declared
  schema, so it is queued rather than closed on the spot.
- The four descriptions state their caps, with what happens past them: the text
  is cut, not refused.
- The subject's shortening is reported in `cut` like the other three. The title
  keeps its `...` and gains no marker — the reason in `Channel::record()`
  stands, that a listing is what a title is read in — but that reason is about
  the file and never reached the answer, which is the half `R-FBK-015` asks for
  and the one that reaches the session still holding the text.
- The cut stays at the tail. The bias the feedback names is real and it is made
  by the description, which asks that the task line come first; the repair that
  matches it is telling the caller the budget, because a report written to the
  cap loses nothing, while a report cut in the middle has already lost the same
  material and lost the join with it.
- Refusing a field past the cap is rejected. `record()` refuses two things
  today, a mangled call frame and an empty observation, and both are cases where
  nothing usable arrived; here a whole report has arrived, and a channel that
  hands it back is a channel a session files nothing through.
- A continuation file is rejected. One feedback is one file with one card, which
  is what lets concurrent agents record at once and what
  [`D-FBK-045`](fbk-045-a-feedback-is-queued-by-the-call-that-records-it.md)
  pairs; a second file carrying half a report would arrive with no card or with
  a second one, and judging.rst has no reading for either.
- Priority `normal`, set by the corpus rather than by the ask. One session
  reported the 4000 cap; 115 recordings hit the subject cap without being told.
- The keep-request is answered and nothing is trimmed. What it asks be kept is
  `R-FBK-015`, held by the two tests named above, so it rests on something other
  than nobody rewriting the class. Both halves it asks *for* are open, and the
  feedback stays whole behind one card.

## Assumed

- That the 115 were subjects. The test is that the title's stem is not how the
  observation opens, and a redaction inside that opening would look the same.
  Nothing counted how many of those there are.
- That a stated cap prevents the loss. It prevents the report that is a
  paragraph too long, which is what the reporting session wrote; it cannot
  prevent one that runs ten characters over, because no caller counts exactly.
- That saying the number costs nothing. A caller told a budget may trim its
  findings to fit it, and that cost would fall on every recording rather than on
  the two that were cut.

## Wrong if

- A feedback loses its assessment after the caps are stated. Tail truncation
  would then be the half worth changing, and stating the number would have been
  the cheaper part of an answer that needed both.
- The subject entry in `cut` reads as noise. It would fire on a quarter of every
  recording, next to a title whose `...` already says a shortening happened
  exactly where it happened.
- The 115 turn out to be redactions rather than subjects. The subject cap would
  then be as rare as the field cap, and the second half of this entry is a
  measurement mistaken for a finding.
- Nobody reads a parameter description far enough to reach the number. The
  `observation` description is already the longest in this server, and a
  sentence added to its end is the one a caller skims past — which would make
  the cap a thing to say in the answer of the previous call rather than in the
  schema.
