---
date: 2026-08-13T21:46:44+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup returns no review comments or votes, so the review skill's "unanswered commen...

## Observation

Task: review Gerrit change 93319 ("[TASK] Add E2E tests for page creation via page wizard") after cherry-picking it onto main.

typo3-core-patch-review makes an unanswered reviewer comment a review surface of its own, in its own words: "A comment somebody left on an earlier patch set and nobody answered is a finding of its own, and it is the one this review would otherwise make a second time", and the checklist repeats it under "The review this patch is already in". It routes that surface to typo3_gerrit_lookup with the Change-Id.

typo3_gerrit_lookup cannot answer it. Its response for change 93319 carried number, subject, status, branch, patchSet, commit, project, updated, url and fetch — and nothing about comments, votes, or labels. The change had 21 patch sets, so this was exactly the case where the surface matters.

I answered it from the checkout instead, by fetching the NoteDB meta ref and reading it as git history:

  git fetch <gerrit-url> refs/changes/19/93319/meta
  git log FETCH_HEAD --format='%an | %s%n%b'

That produced the whole review history: every Verified/Code-Review vote with its author, the CI bot's pipeline URLs, the patch-set descriptions ("Rebase", "Edit commit message"), and the "Outdated Votes" blocks. Two facts came out of it that changed my report and were reachable no other way:

- PS21 carries 2x Verified+1 (CI green, pipeline 106666), but the Code-Review+1 given on PS20 was dropped by the PS21 rebase — copy condition "changekind:NO_CHANGE OR is:MIN". The change therefore has NO current Code-Review vote, which is the single most useful thing to tell a reviewer picking it up.
- PS10, 12, 15 and 16 each took a Verified-1. Four CI failures across 21 patch sets on a Playwright-only diff is direct evidence for the flakiness findings I was writing, and it moved two of them up my ranking.

Neither is derivable from the fields typo3_gerrit_lookup returns. The gerrit-workflow document mentions the meta ref, but only to warn it "is its review history and not a commit anybody builds on" — it does not say it is where the comments are, and reading it as the answer to the review surface was my own step.

Two lesser gaps in the same answer: the change's own Change-Id is not returned (I had it from the commit message, but the skill tells you to hold what came back against the commit under review), and the returned `changeId` field was empty in the reviews[] array of typo3_forge_lookup for the same change.

## Query

typo3_gerrit_lookup(change: "93319") — task: review change 93319 patch set 21 and report findings in priority order

## Suggestion

Return the review state with the change: the current label values per category (Code-Review, Verified) with their voters, whether a vote was outdated by the last patch set and by which copy condition, and the human comments — change-level and inline, with file and line — each tagged with the patch set it was left on and whether anything came after it. Distinguishing bot comments from human ones matters as much here as it does in typo3_forge_lookup, which already has a `notes` parameter for exactly that: on this change all 12 Forge notes were bot notes, and on Gerrit all comments but one were the CI bot. A `comments: "all" | "people" | "none"` parameter mirroring that would keep the answer readable.

Failing that, say in the tool description that the meta ref is where the review history is readable from, and what its commit bodies look like — it is a working fallback but it took me two calls and a hand-written grep filter to discover.
