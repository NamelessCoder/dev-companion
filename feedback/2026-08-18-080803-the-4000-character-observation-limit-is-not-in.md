---
date: 2026-08-18T08:08:03+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_feedback_record
directory: /home/benji/projects/blog
---

# The 4000-character observation limit is not in the parameter description, so it is only discovere...

## Observation

Task: fix ext:blog's [blog.isPost()] / [blog.isPage()] TypoScript conditions on TYPO3 v14 without breaking v13; this was found while filing the debrief afterwards.

observation has a 4000-character cap that its own description does not mention. The description asks for a lot — open with the task, be specific enough to act on later, give the path a value was read at and the shape of what came back, keep secrets out — and none of that hints at a budget. So I wrote to the brief, not to a limit, and lost the last 650 characters of feedback/2026-08-18-080710 without seeing which ones.

Two things soften this and should be kept. The result did report the cut rather than swallowing it: "cut": ["observation: 650 characters past the 4000-character limit"] is explicit about the field, the amount and the limit, so I could tell immediately that something was gone and roughly how much. And there is a "redacted" channel beside it, which suggests the same reporting discipline applies to secret-stripping. That is better behaviour than most truncation.

What it cost: the material that fell off was the tail, which in my case was the positive findings — what the server did well — plus a note on the guides list and my own Bash errors. That is a systematic bias worth naming: a caller following the description writes the task line first and the assessment last, so a tail-truncation preferentially destroys "what worked". The instruction to open with the task guarantees the least disposable content sits at the end. I refiled the lost part as a separate note, but only because I read the result carefully; a caller who does not would ship a feedback whose conclusion is missing and never know.

Other minLength-carrying fields (subject, suggestion, query) presumably have caps too. None are stated either, and I do not know whether they truncate or reject.

## Query

typo3_feedback_record with a long observation, filed from /home/benji/projects/blog. Result carried: "cut": ["observation: 650 characters past the 4000-character limit"]. The observation parameter's own description states no limit.

## Suggestion

State the cap in the observation parameter description — "at most 4000 characters; longer text is truncated, not rejected" — and do the same for subject, suggestion and query with their real numbers. A caller who knows the budget writes to it; a caller who does not writes past it once per session.

Given the tail bias, consider one of: truncating from the middle with an explicit marker so the opening task line and the closing assessment both survive; or rejecting past the limit so the caller resubmits rather than silently shipping a decapitated note; or accepting the overflow and splitting it into a linked continuation file.

Keep the "cut" and "redacted" fields in the result exactly as they are. Reporting what was dropped, by field and by amount, is why this was recoverable at all.
