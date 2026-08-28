---
date: 2026-08-28T07:40:58+00:00
category: bug
status: closed
closed: 2026-08-28
model: claude-opus-5[1m]
tool: typo3-extension-patch-review, typo3-extension-health, typo3-content-element-development, typo3-extension-testing
directory: /home/benji/projects/bootstrap_package
---

# the deprecation sweep exemption has no second gate when a review turns into a change

## Observation

Task: review pull request #1613 against bootstrap_package, then — after the report — fix it and cover it with a test.

references/base.md, shared by the skills here, exempts a workflow that writes nothing from the deprecation sweep: "A task that produces no change does not reach this step at all." It then closes the exemption explicitly: "The exemption ends where the workflow produces a change. A review asked to make the change is that other workflow, and it starts this order again holding the files it is about to write."

That is precisely the shape of this session. I started under typo3-extension-patch-review and correctly skipped the sweep, saying so in the report. The user then wrote "bitte fixe den patch, kannst du es mit einem test auch abdecken?". I invoked typo3-extension-health, worked its steps 1 to 8, crossed to typo3-content-element-development for the template edit and to typo3-extension-testing for the test, committed, and closed. I never ran typo3_changelog_lookup with type=deprecation on either declared major. I did not notice until this debrief, reading the transcript rather than recalling it.

Why it slipped, concretely: the exemption is stated once, in base.md, at the moment the sweep is described — that is, at the point where I was deciding to skip it. The sentence that closes the exemption sits in the same paragraph, minutes earlier in the session than the moment it applies. When the request to change arrived, I re-entered through typo3-extension-health, whose own step list starts at "work through references/base.md" — but by then base.md was already in my context from the review, so I did not re-read it, and nothing in health's steps 5 to 13, nor in content-element-development's or testing's own step lists, asks whether the sweep now owes.

The cost here was low: the change touches one Fluid attribute and adds a test, so a sweep would almost certainly have come back with nothing bearing on it. The cost is not the point. The exemption is designed to be re-opened by a second gate that does not exist, and a session that skips it correctly the first time has no prompt to revisit it.

Reported as a bug rather than an idea because the documents already state the intended behaviour and the workflow does not produce it.

## Query

Skill typo3-extension-patch-review, references/base.md read whole (step 5, deprecation sweep and its exemption).

Then, after the user asked for the fix: Skill typo3-extension-health, Skill typo3-content-element-development, Skill typo3-extension-testing.

typo3_changelog_lookup was never called in this session, on either 13 or 14.

## Suggestion

Put the gate where the crossing happens rather than where the exemption is granted.

typo3-extension-health's step 5 ("write one item per finding") is the moment a review becomes a change, and it already knows both things it needs: that the preceding workflow was a review, and which paths the items touch. A line there — "where this list was built from a review that took the sweep exemption, the sweep is owed now, before the first edit; name it as item zero" — would fire at the right moment.

The same holds for the two skills health crosses into. typo3-content-element-development and typo3-extension-testing both open with "work through references/base.md first", which a session that already has base.md in context reads as satisfied. If the sweep is genuinely owed per change rather than per session, those openings should say so in words that distinguish "read this document" from "run this step again for the files you are about to write".
