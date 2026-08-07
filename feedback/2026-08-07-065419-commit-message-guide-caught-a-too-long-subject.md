---
date: 2026-08-07T06:54:19+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide, typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# commit_message_guide caught a too-long subject and validated the release branches

## Observation

Task: verify Forge 109572 and write the core patch, ending in a commit with Gerrit trailers.

typo3_commit_message_guide was called four times across the session, each time after the change itself had moved, and it earned every call. On the first draft it flagged summary-length-preferred: 62 characters, a 53-character summary plus the 9-character keyword. That is exactly the check a human reviewer would otherwise raise on Gerrit, and it cost one round trip to fix. It also returned breaking-not-assessed as an info check, saying the classification had been assumed rather than checked because it never sees the diff — which prompted me to pass isBreaking and isDeprecation explicitly after actually looking at what the diff removes, rather than leaving it implied.

The Releases validation mattered most. I had drafted "main, 13.4" from my own reading. When the user said the targets must be 14.3, main and 13.4, I re-ran the guide with all three and it accepted them, held against the branches that take a patch today. I could not have got that from the checkout, and getting it wrong is the kind of thing that stalls a change in review.

The 72-character body wrapping being done for me was also worth having — the returned message was directly commitable, and I wrote it to a file and passed it to git commit -F unchanged.

## Query

typo3_commit_message_guide(changeType: "BUGFIX", summary: "Find non-nullable date columns when querying for null", body: ..., issue: "109572", releases: ["main", "13.4"], workflow: "core") — returned summary-length-preferred and breaking-not-assessed; then re-run with a 40-character summary, isBreaking false, isDeprecation false, and finally releases ["main", "14.3", "13.4"]

## Suggestion

Keep the summary-length check, the breaking-not-assessed info check and the Releases validation against currently supported branches. All three caught something a checkout cannot tell you. One small idea: the info check says the classification was assumed because the tool never sees the diff — it could name the concrete things to look for in the diff (a removed or narrowed public or protected member) inline rather than routing to typo3_rule_lookup, since in this session I answered the changelog question from my own knowledge instead of making that second call.
