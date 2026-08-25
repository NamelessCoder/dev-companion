# Settle a core patch's scope against every point its issue lists

**Serves:** feedback/2026-08-24-162543-cover-every-point-a-forge-issue-lists-or-split.md, D-SKL-075
**Priority:** normal

Step 4 of the ladder, wording: `typo3-core-patch-development` fired, its step 3
carried all three points of Forge #106584, and the session shipped two of them.
The sentence that governs a split — "Keep the patch one change. What else you
noticed is another issue and another patch" — is about work arriving from
outside the issue and argues for the narrowing rather than against it.

Write the other direction into the skill: the points the issue lists are
enumerated while it is assessed, and one patch covers all of them or each
remainder is given its own issue before any code, because the `Resolves:`
trailer and the changelog file name each take a number. Where it goes is open —
a sentence beside "Keep the patch one change", or a rung of the assessment
section — and `D-SKL-010`'s **Wrong if** warns that a skill growing a sentence
per feedback stops being an order.
[writing-a-skill.rst](../../documentation/contributing/writing-a-skill.rst) is
what the wording is held to, and whether this is also a requirement is part of
the work.

`typo3_forge_lookup`'s description is not touched: `D-SKL-075` records why the
feedback's second half lands in the skill instead.
