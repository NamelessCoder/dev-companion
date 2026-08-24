---
date: 2026-08-24T12:23:30+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup settled the whole review question in one call, commit SHA included

## Observation

Task: review Gerrit change 95179 and work off the review comments. This is the part that must not be broken later.

One call — typo3_gerrit_lookup(change: "95179", messages: "people") — answered every question the task opened with:

- the three comments in full, each with author, patch set, timestamp, the thread it replied to, and an `unresolved` flag. That flag is what let me separate "Mathias asked for an Important RST and questioned the v13 backport (unresolved)" and "Georg said the same and marked his own resolved" from noise, and it told me the owner's own "sure! will check it later" was still open. No browsing, no guessing which feedback was outstanding.
- the labels with the actual voters and values, so I could see the owner had put Code-Review-1 on their own change and had to tell them to lift it.
- branch main, status NEW, patch set 1, and the fetch ref.
- crucially `commit: 0b18ff0af75d3dbae5a28f92d0abf9a4a1be7870`. That SHA was already a loose object in the local checkout (git for-each-ref --contains returned nothing, no branch pointed at it). Because the lookup handed me the SHA I could `git checkout -b bugfix-81619 0b18ff0` and rebase onto origin/main instead of fetching refs/changes/79/95179/1 over the network. That is a concrete round trip and a network dependency removed.
- `chain: []`, which told me straight away this was a standalone change and not one step of a stacked series — a thing I would otherwise have had to establish from Gerrit by hand.

Note that I passed the change NUMBER and the tool echoed `query: change:I4a7557ccf3dc68bd6dc7dc40a5fa269bad0f6aa8` — it resolved the number to the Change-Id before searching. That worked and is the right behaviour; I mention it only so it is not "fixed".

typo3_forge_lookup(issue: "81619") complemented it well: status Under Review, tracker Bug, priority Must have, target "Candidate for patchlevel", and the 2017 reporter's own framing of the bug. I reused that framing in the Description section of the changelog entry I wrote, which is a better source than my own paraphrase of the diff. notes: "all" cost nothing here — the issue had exactly one note and it was the Gerrit bot, correctly counted in botNoteCount.

typo3_project_describe gave me TYPO3 15.0.0-dev, PHP 8.5 and the DDEV environment in one shot before I touched a file.

## Query

typo3_gerrit_lookup(change: "95179", messages: "people"); typo3_forge_lookup(issue: "81619", notes: "all"); typo3_project_describe(). Three calls, two round trips (the first two ran in parallel with unrelated work).

## Suggestion

Keep the unresolved flag, the inReplyTo threading, the per-voter labels, the commit SHA and the empty-chain signal exactly as they are. The commit SHA in particular is what makes a loose local object findable without a network fetch, and it is not obvious from the field list that it is load-bearing.
