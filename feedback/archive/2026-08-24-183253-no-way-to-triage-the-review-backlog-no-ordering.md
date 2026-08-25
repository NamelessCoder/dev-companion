---
date: 2026-08-24T18:32:53+00:00
category: tool-gap
status: closed
closed: 2026-08-25
model: claude-opus-5
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# no way to triage the review backlog: no ordering by age, no size, no vote state in a search

## Observation

Task, in the user's words (German): find open reviews on Gerrit that have been sitting around a long time, work some off, pick the ones that would have enough votes and are small in scope, take one, test it through and do a full review, fix merge conflicts if it has any — starting with the older ones.

That is the whole first phase of the session and no tool here carried any of it. typo3_gerrit_lookup answers one change well, and its `open` flag narrows a query or a path to what is still under review, but it cannot enumerate. What the task needed and the schema does not offer:

- an ordering by when a change was created or last touched. There is no sort argument, and `limit` caps at 25 with no offset, so "the oldest open changes" cannot be asked at all.
- the size of a change. insertions/deletions are in Gerrit's own ChangeInfo but not in what comes back here, so "small in scope" is unanswerable.
- the vote state of a search result. A change read by `change` carries its labels; a result from `query` or `path` does not, so "would have enough votes" is unanswerable.
- whether it still merges. `mergeable` is likewise only in the per-change answer.

So I went to the REST API directly: four paginated curl calls against review.typo3.org/changes/?q=project:Packages/TYPO3.CMS+status:open&n=500&S=<n>&o=DETAILED_LABELS, 859 open changes, then a Python scorer over created date, insertions+deletions, Code-Review +1/+2 counts, absence of any negative vote, mergeable and unresolved_comment_count. 49 candidates sorted oldest first. That produced the shortlist I picked 91127 from, and I would write the same script again next session.

What makes this a gap rather than a wish: typo3_forge_lookup already has exactly this shape for the issue tracker — open: "oldest" | "stale", createdBefore, updatedBefore, tracker, category, breakdown. Its description even calls it "where a triage of the backlog starts". The review server has the same backlog and the same question asked of it, and the asymmetry is invisible until you try.

Two things did work well once I had a number and should not be touched: typo3_gerrit_lookup with change=91127 gave me patch set 8 with its commit hash and the fetch ref, so I fetched the revision actually under review rather than patch set 1; and it carried the comment threads with unresolved flags, which is how I found Oliver Klee's still-open "Is there any way this can be covered with a functional test?" from 2025-12-09.

## Query

No call produced this — that is the finding. What I wanted to ask: typo3_gerrit_lookup(open: "oldest", maxSize: 60, minCodeReview: 1, mergeable: true, limit: 25) for project Packages/TYPO3.CMS. What I did instead: curl "https://review.typo3.org/changes/?q=project:Packages/TYPO3.CMS+status:open&n=500&S=0&o=DETAILED_LABELS" four times and scored the 859 results locally.

## Suggestion

Give typo3_gerrit_lookup the enumeration typo3_forge_lookup already has: an `open` argument taking "oldest" (by created) and "stale" (by updated), with createdBefore / updatedBefore and a branch filter. Carry three fields into every row of an enumeration and of a query/path search, all of which Gerrit returns for free with o=DETAILED_LABELS: insertions/deletions, the Code-Review and Verified tallies, and mergeable. Those three are exactly "small", "has votes" and "still applies", which is how a person describes a reviewable patch.

A breakdown on the same set would answer the other half of the request — how the open backlog is distributed by age, by target branch, by size band — which is what decides whether a review session is worth starting at all.
