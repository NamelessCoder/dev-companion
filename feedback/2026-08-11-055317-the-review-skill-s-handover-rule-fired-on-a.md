---
date: 2026-08-11T05:53:17+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# The review skill's handover rule fired on a reviewer's remark about a finding and switched me int...

## Observation

Task: review Gerrit change 94686. The skill fitted the task well and I would keep almost all of it — the surface checklist, the "what a dropped candidate owes" section and the instruction to name the suites I did not run all changed what I wrote. One part misfired.

Its closing section says the reader accepting the findings and asking for the change ends the review, that this "looks like nothing at all from the inside — a sentence in a conversation", and that at that sentence I must invoke typo3-core-patch-development.

I had just reported that the patch changes covered behaviour and adds no test. The user replied: "ich denke die tests sollten es belegen" — "I think the tests should prove it". I read that as the handover sentence and invoked typo3-core-patch-development. The user corrected in the next message: "das ist ein grund es abzulehnen da es tests in dem bereich gibt" — "that is a reason to reject it, since there are tests in that area." They were not asking me to write tests; they were telling me the finding was strong enough to be a rejection reason, because a dedicated test file already exists for that class. I backed out and stayed in the review, and re-ranked the finding from "sent back in review" to "blocks submission".

The real handover came two turns later and was unambiguous: "ich möchte das wir ihn fertig machen" ("I want us to finish it").

The section's warning is aimed at the failure of *not noticing* the handover. In this session the opposite happened: the warning primed me to treat an evaluative remark about a finding as an instruction to change code. A sentence about what the patch owes and a sentence telling me to make it are easy to confuse in exactly the register a reviewer talks in.

## Query

Skill typo3-core-patch-review invoked with args "94686". Its closing section "Where the review ends and the rework begins" then fired on the user message "ich denke die tests sollten es belegen" ("I think the tests should prove it"), and I called Skill(typo3-core-patch-development).

## Suggestion

In "Where the review ends and the rework begins", add the counter-case beside the existing one: a remark about a finding's weight — "yes, that is a reason to reject", "I think the tests should show that", "that one blocks it" — is still review, and reaffirms a finding rather than commissioning work. The handover is an instruction to change the patch, and where the sentence could be either, ask rather than switch: switching costs a wrong skill's rules for a turn, asking costs one sentence. Consider naming a positive marker ("finish it", "fix it", "amend it", "write it") rather than only warning that the transition is invisible.
