---
date: 2026-08-08T22:44:06+00:00
category: idea
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_project_describe, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# the guides are reachable only through project_describe's tail and I read none of them whole

## Observation

Task: pick an old core bug, fix it, prepare it for review. My client rendered no resource list at any point in the session. The only reason I knew the guides exist is that `typo3_project_describe` ended with a `guides` array of nine ids — exactly as the server's own instructions promise — and I read it there.

I then read none of them whole. `typo3-core-patch-development` tells a session in plain words to read `core/contribution/commit-messages` end to end: "reading it once here is cheaper than learning it from checks one call at a time". I did the opposite and ran a single `typo3_rule_lookup(query="bugfix changelog entry obligation and target branches")`. It returned two sections — "Changelog Files" and "Release Targets" — and both happened to be exactly what the patch owed, so I never noticed a gap. That is luck rather than method: one query matched both of the two things a BUGFIX needs to decide, and a differently worded query would have returned one of them and left me believing I had the answer.

I also never opened `core/contribution/gerrit-workflow`, correctly, because the session stopped short of pushing. But that means the one guide the skill names as must-read-before-first-push went unread in a session that produced a push-ready patch, and nothing at the end of the work said so.

Concretely: a `documentId` call is available and cheap, I knew it was available from the `guides` array and from the `documentId` parameter description, and I still chose a search — because the search answered and answering is indistinguishable from answering completely.

## Query

typo3_project_describe, then typo3_rule_lookup(query="bugfix changelog entry obligation and target branches", targetVersion="15") — and no typo3_rule_lookup(documentId=…) call at all

## Suggestion

Make a `typo3_rule_lookup(query=…)` answer say what fraction of its document it is: a line like "these are 2 of the 7 sections of core/contribution/commit-messages — read it whole with documentId" turns a satisfied search into a visible partial. The existing `coverage` and `score` numbers do not communicate that, and a session cannot tell a query that matched everything relevant from one that matched the first thing relevant. Consider having the skills that name a guide say the id in the sentence that names it, so `typo3_rule_lookup(documentId="core/contribution/commit-messages")` is copyable rather than something the session has to know to assemble from `typo3_project_describe`'s tail.
