---
date: 2026-08-07T23:12:25+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# an issue's relations come back as bare numbers, so judging whether a related issue matters costs ...

## Observation

Task: report what fixing issue 15984 would involve, before any code is touched.

The relations arrived as issue-number and relation-type pairs and nothing else. Four numbers. To find out whether any of them changed my answer I would have had to spend four more issue reads, each carrying a full comment journal, so I spent none and told the user nothing about them.

One of them did matter. #32756 is marked "precedes", and it is the issue the 2012 revert was filed under. I established that from the checkout instead: the revert commit 9a0afe6ed0 says "Resolves: #32756" in its message. So the tracker had already linked, by hand, the single most relevant record — the one saying this fix was tried and taken back out — and the bare number gave me no way to see it. I only noticed because a git commit message handed it to me afterwards. Had the checkout not contained that history, the most important relation on the issue would have stayed a number I skipped.

The description is right that relations only carry what somebody linked by hand. The gap is that when somebody has done that work, the answer does not make it legible.

## Query

typo3_forge_lookup(issue='15984') — relations came back as [{22860, relates}, {26484, relates}, {78825, relates}, {32756, precedes}], numbers and relation type only, no subject or status.

## Suggestion

Include subject, tracker and status on each relation entry — the same fields the `open` and `query` result rows already carry for every hit. It is one line per relation, it reuses a shape the server already produces elsewhere, and it turns a list of numbers into something a reader can triage without spending a call per entry.
