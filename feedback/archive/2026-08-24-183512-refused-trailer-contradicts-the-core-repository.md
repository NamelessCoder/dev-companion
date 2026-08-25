---
date: 2026-08-24T18:35:12+00:00
category: idea
status: closed
closed: 2026-08-25
model: claude-opus-5
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# refused-trailer contradicts the core repository's own AGENTS.md on Signed-off-by

## Observation

Task: check the commit message of Gerrit change 91127 before proposing an amended patch set, from inside the TYPO3 core checkout at 15.0.0-dev.

typo3_commit_message_guide with workflow="core" strips Signed-off-by and returns an `error`-level check: "The Signed-off-by: line is off the draft. A core commit message carries Resolves, Related, Releases and the Change-Id the hook writes, and nothing else — whatever the checkout you are working in says."

That last clause reads as if it anticipates a disagreement, and there is one. AGENTS.md in this very repository, which is checked in and which the CLAUDE.md imports as authoritative project instructions, says the opposite in two places: it lists Signed-off-by in the mandatory footer order, and it says "Sign off every commit — `git commit -s` appends the Signed-off-by: trailer, or set `git config format.signOff true` to get it automatically. It certifies that you wrote the patch, or otherwise have the right to submit it under the project's licence (Developer Certificate of Origin)."

I measured which is right: `git log origin/main -200 --format=%b | grep -c '^Signed-off-by:'` returns 2. So in practice the server is describing the core correctly and AGENTS.md is describing an intention the project does not enforce. But two of two hundred is not "nothing else", and the change I was reviewing is one of them — its author put it there deliberately, it has survived eight patch sets and several rebases by other people, and removing another contributor's DCO sign-off while amending their patch is not a thing an assistant should do on a tool's say-so.

The practical result: I kept the trailer, told the user why, and flagged that the guide disagrees. That is the right outcome but I had to establish it from the checkout and from git history, and a session that simply obeyed the error would have quietly stripped a DCO attestation from someone else's patch.

The clause "whatever the checkout you are working in says" is the part I would change. It is aimed at a checkout with a local convention, but in this case the checkout is the TYPO3 core itself and the file it is overruling is the core's own contributor guidance.

## Query

typo3_commit_message_guide(workflow: "core", message: the full commit message of Gerrit change 91127 including "Signed-off-by: Torben Hansen <derhansen@gmail.com>" and "Change-Id: Ibb426e12fe37d89471c4b7fa8cb11fade77ba5f3") — read the refused-trailer check. Compare against AGENTS.md in the core checkout, section "Commits & review".

## Suggestion

Downgrade refused-trailer from error to info when the trailer is Signed-off-by and the message being checked already carried it, and say what is actually true: the core does not require it, almost no merged commit has one, the commit-msg hook accepts and preserves it, and it is not removed from somebody else's patch when amending. That is four facts, all checkable, and together they let a caller decide rather than obey.

If the intent is to stop an assistant adding its own sign-off to a fresh message, say that specifically — "do not add one you were not given" is a different and much better rule than "a core commit message carries nothing else".

And the clause "whatever the checkout you are working in says" is worth softening or naming: in a core checkout the thing it overrules is AGENTS.md, which contributors are told to follow. Either the server or that file is wrong, and the two are currently in direct contradiction on a point that touches licence attestation.
