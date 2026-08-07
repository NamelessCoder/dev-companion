---
date: 2026-08-07T13:24:16+00:00
category: wrong-answer
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# a private Gerrit change is reported as empty, and reads as "no patch exists"

## Observation

Task: review a local TYPO3 core commit ("[BUGFIX] Handle empty dates consistently in Extbase", Forge #109572) against a v15 core checkout, critically and completely.

Three calls, three misleading answers. Lookup by the Change-Id carried in the commit message returned {"status":"empty","changes":[]}. Lookup by issue 109572 returned {"status":"empty"}. Lookup by the change number 95162 returned {"status":"unavailable","cause":"source-not-answering","reason":"The review server did not answer."}.

Meanwhile typo3_forge_lookup on the same issue returned a journal entry authored by "Gerrit Code Review" dated 2026-08-07T06:48 saying patch set 1 for branch main had been pushed to https://review.typo3.org/c/Packages/TYPO3.CMS/+/95162, plus a comment from another contributor saying "i work on that".

I read "empty" as "this commit was never pushed" and read 95162 as somebody else's competing change. I made that the first finding of the review, ranked under "what blocks the patch from being submitted at all", and recommended coordinating with the other author before pushing. The user then told me the change is private on Gerrit and is the same commit that is checked out. Every one of the three statuses was a permission effect of an anonymous read against a private change; none meant what it said, and I had no way to tell.

Two details worth keeping: the same underlying cause produced two different statuses ("empty" for the searches, "source-not-answering" for the direct read by change number), and the tool description frames the call as "Find out whether a TYPO3 core patch already exists" — which is precisely the reading that fails here.

This compounds with the typo3-core-patch-review skill, which instructs: "A patch that is not pushed yet has no change, and an answer of nothing is a result: say so rather than leaving the surface silent." That instruction is only safe while empty really means absent.

## Query

typo3_gerrit_lookup change="I7701923d80dbd29377213fa71c74ecad88cf7d31"; then typo3_gerrit_lookup issue="109572"; then typo3_gerrit_lookup change="95162"

## Suggestion

Distinguish "no change matches this query" from "nothing an anonymous reader may see". The server reads Gerrit without credentials, so a private or work-in-progress change is indistinguishable from a missing one — say that in the answer whenever the query named a concrete Change-Id or change number and came back empty, rather than only in the tool description. Two concrete improvements: (1) report the same status for both failures instead of "empty" for a search and "source-not-answering" for a direct read, or say why they differ; (2) where the query is a Change-Id taken from a commit message that exists locally, an empty answer is far more likely to be a restricted change than a never-pushed one, and the answer could say which of the two it cannot distinguish. Bonus signal the server already has: if typo3_forge_lookup would surface a review URL for the same issue, an empty gerrit answer for that number is positive evidence of a restricted change.
