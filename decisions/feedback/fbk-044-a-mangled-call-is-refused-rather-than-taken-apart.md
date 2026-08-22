---
id: D-FBK-044
title: A mangled call is refused rather than taken apart
date: 2026-08-04
status: open
coveredBy:
  - FeedbackTest::aFieldCarryingTheCallItArrivedInIsRefused
  - FeedbackTest::aReportQuotingTheMarkersIsStillRecorded
---

# D-FBK-044 — A mangled call is refused rather than taken apart

**A feedback field arriving with the frame of its own call in it is refused, and
the thirty-four already stored are repaired once by hand.**

Two batches of feedback were written with their suggestion buried in the middle
of the observation, because the parameter had been closed with a tag named after
itself and swallowed everything behind it.

## Evidence

- 34 of the 270 feedback in the corpus carry the frame: 20 from 2026-07-29,
  before the `model` field existed, and 14 from 2026-08-04 by `claude-opus-5`.
  Two clients, five days apart, so it is not one session's slip.
- The shape is the same in all 34: the observation ends in `</observation>`,
  then one `<parameter name="suggestion">` block, closed with `</suggestion>`,
  with `</parameter>` or with nothing, sometimes followed by `</invoke>`.
- The 2026-08-04 session is readable end to end — transcript `4b813d94` in
  `/home/benji/projects/site-new`, 13:23 to 18:02, no compaction. It made 456
  tool calls. The 14 malformed ones are all `typo3_feedback_record` and every
  other call is clean, 47 `Write` and 55 `Edit` with long multi-line content
  among them. The two feedback schemas arrived through `ToolSearch` at 17:56,
  two minutes before the first record.
- What the client stored as the call's input names five arguments — `category`,
  `model`, `tool`, `query`, `observation` — and no `suggestion`. The schema has
  it optional, so nothing rejected the call and the writer omitted a heading for
  an argument that never arrived.

## Decided

- **Refused, not reconstructed.** A server that takes another party's broken
  protocol frame apart guesses at the shape of somebody else's bug on every
  write, and afterwards nothing distinguishes the guess from the report. A
  refusal is actionable while the session is still running: the 14 calls were
  seconds apart, and the first rejection would have corrected the other 13.
- The check is **structural rather than topical**: a field that ends in the
  call's closing tag, or a parameter opening a line of its own. A feedback
  *about* this failure quotes those markers inline and mid-sentence, and a test
  holds that such a report is still recorded.
- All three prose fields are checked. Which one swallows the rest depends on the
  order the parameters were emitted in, and nothing here decides that order.
- The 34 stored ones are repaired in the commit that adds the guard: the markers
  go, the suggestion moves into the section it was written for, and no other
  word is touched — every removed line was checked back into the file it came
  from. One diff a reviewer can read is not the rule this entry refuses to be.
  `D-FBK-039` did the same for 43 mangled names.

## Assumed

- That the emitted call closed each parameter with a tag named after itself. The
  transcript keeps the parsed input rather than the model's text, so what is
  established is the result — the tail of the call inside the first field — and
  the cause is read back from it.
- That a refused session re-sends. Every model this repository has feedback from
  retries a tool error, and the message names what to change.

## Wrong if

- A session is refused and files nothing at all. Then the report is lost where a
  split would have kept it, and taking the call apart with a marker saying so is
  the answer after all.
- A legitimate report is refused for quoting the markers. `FeedbackTest` holds
  the one case that exists, and a second shape would show the check is reading
  the subject rather than the structure.
- The frame arrives with the parameters in another order, or from a client that
  builds them differently, and the check does not see it.
