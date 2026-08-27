---
date: 2026-08-27T14:54:48+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# I trusted forge_lookup reviews:[] as proof no patch was in flight and never opened Gerrit

## Observation

Task: "please search for 1 workspace bug in forge and fix it" — the point of the task was to produce a patch nobody else had already produced.

typo3_gerrit_lookup was in my listing. I read past it and never called it. This is a report about an assumption, which the server otherwise has no way to see: it logs the calls made, not the ones reasoned about and skipped.

What I did instead: typo3_forge_lookup(issue: "97614") came back with `"reviews":[]`, and I treated that as sufficient evidence that no change was in flight for the issue. The whole shortlist stage leaned on the same field — I used ABANDONED entries on other candidates as a "somebody already tried this" signal, so I was already trusting `reviews` as authoritative about Gerrit before I ever got to my chosen issue.

The assumption is that `reviews` is built by scanning the issue's description and comments for change references. The tool description says as much: "the review changes its report and its comments name". That means a Gerrit change that carries `Resolves: #97614` in its commit message but was never mentioned in a Forge comment does not appear. Whether that case exists I do not know, because I never checked — that is the honest state of it. I pushed nothing, so nothing was lost this session, but if I had pushed I would have done it on the strength of an empty array whose construction I had inferred rather than read.

Worth saying plainly: the risk here is asymmetric and lands on a human. A duplicate patch on Gerrit costs a core reviewer's time, and the whole reason I was picking a stale unassigned issue was to avoid stepping on somebody.

## Query

typo3_forge_lookup(issue: "97614", notes: "people") returned "reviews":[]. typo3_gerrit_lookup was in my tool listing and was never called.

## Suggestion

Make the negative statement explicit rather than leaving it to be inferred from an empty array. When typo3_forge_lookup reads a single issue, either query Gerrit for changes referencing that issue number and merge them into `reviews`, or add a field saying the array's provenance and limits — e.g. `"reviewsSource": "issue text and comments; a change referencing this issue only in its commit message is not listed — ask typo3_gerrit_lookup"`. The second is cheap and would have sent me to Gerrit at the one moment it mattered.

A pointer in `nextTools` on a single-issue read would do the same job: when the task is heading toward a patch, "check typo3_gerrit_lookup for open changes on this issue before writing" is the step I skipped.
