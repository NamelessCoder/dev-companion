---
date: 2026-08-07T23:11:46+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# Gerrit change references arrive as comment prose, so the follow-up to typo3_gerrit_lookup never h...

## Observation

Task: establish whether issue 15984 still reproduces on a 15.0.0-dev checkout and report what fixing it would cost.

The user's real question was what they would be signing up for. For that, the single most valuable fact is why the most recent fix attempt was abandoned. Both issues I read named Gerrit changes: #15984 carried review.typo3.org/1186, /2544 and /2545 from 2011, and #14858 carried /70962, which Sybille Peters recorded as abandoned in 2023. typo3_gerrit_lookup exists for exactly this. I never called it — I never even loaded its schema.

The reason is how the references arrive. They sit as free text inside journal entries, next to "Patch set 3 ... has been pushed to the review server". Read as prose they look like history already told, not like a handle to a second lookup. Nothing in the answer said a tool could open them.

I closed the gap from the checkout instead. git log --all --grep=15984 gave me the 2011 fix 94feeb2596 and the 2012 revert 9a0afe6ed0, and git show gave me the thirteen-line reverted diff, which was enough to tell the user which design to avoid — the rootline walk went inside the shared group-access check, so it landed on a hot path. That part worked. But the 2021 attempt on change 70962 is the most recent evidence of what a fix looks like against a modern core, and I reported it only as "abandoned", because that is all the Forge comment said. A checkout cannot answer that one. Gerrit can.

## Query

typo3_forge_lookup(issue='15984') and typo3_forge_lookup(issue='14858'); the typo3_gerrit_lookup calls for changes 1186, 2544, 2545 and 70962 that I never made. Task text: "tell me whether it is still a thing. I want to know what I would be signing up for before I touch it."

## Suggestion

Return the review.typo3.org change references an issue's journal mentions as a structured field on the issue answer — change number, and status where known — instead of only inside comment text, and name typo3_gerrit_lookup as the way to read them. An issue answer that ends "changes 1186, 2544, 2545, 70962 are referenced; read them with typo3_gerrit_lookup" turns a passed-over tool into an obvious next call. The abandoned patch is usually the cheapest answer to "what would this cost", and it is currently the hardest thing in the payload to notice.
