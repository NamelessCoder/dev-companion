---
date: 2026-08-05T22:11:12+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# Task: write the commit message for a core bugfix patch against Forge #76536, after the change and...

## Observation

Task: write the commit message for a core bugfix patch against Forge #76536, after the change and its test were done.

The summary-length-preferred check reports the length of the assembled subject line, but the parameter it appears to be about is `summary`, which excludes the keyword. I passed a 57-character summary and was told "The summary line is 66 characters long" — 66 being 57 plus "[BUGFIX] ". Nothing in the message says the nine characters of the prefix are in that count, and "the summary line" is the same phrase the parameter uses, so I read it as a claim about what I had passed and shortened by nine characters. The second call passed a 51-character summary and was told 60. Only then did the arithmetic become obvious and I shortened to 33, which passed clean.

Three round trips for what one could have carried. Everything else about the tool was good and I would not change it: the missing-releases check naming the lines taking a patch today (main, 14.3, 13.4) was directly load-bearing, since the checkout's own branch listing shows 13.0, 13.1, 13.3, 14.0 and 14.1 as well and I would have had to work out which of those are maintained; and the body-lines-reflowed notes told me exactly what the wrapper had done to paragraphs I had hard-wrapped myself.

## Query

typo3_commit_message_guide with workflow="core", changeType="BUGFIX", issue="76536", and summary="Implode array placeholder values on every recursion level" (57 characters). Check returned: "The summary line is 66 characters long. Below 52 characters is preferred."

## Suggestion

Say whose length is being reported, and by how much the prefix contributes. Something like: "The subject line is 66 characters long: 9 for the [BUGFIX] keyword plus a 57-character summary. Below 52 characters in total is preferred, so the summary has room for 43." That makes the first answer sufficient — the caller knows what to cut and by how much — instead of teaching the arithmetic through two failed attempts. The same applies to the hard 72-character check if it counts the prefix too.
