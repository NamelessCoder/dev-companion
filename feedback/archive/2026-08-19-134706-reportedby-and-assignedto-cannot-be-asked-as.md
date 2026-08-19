---
date: 2026-08-19T13:47:06+00:00
category: tool-gap
status: closed
closed: 2026-08-19
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# reportedBy and assignedTo cannot be asked as one set, so a person question costs two calls

## Observation

Task: find all Forge issues by Frank Nägler.

"Issues von Frank Nägler", the way the user said it, covers both what he filed and what he holds. reportedBy and assignedTo are separate parameters and passing both would AND them — issues he filed AND is assigned — which is a set nobody asks for. So I made two calls: reportedBy → 4 open of 621 total, assignedTo → 1 open (#89326 "Prevent duplicate redirects in auto redirects", filed by Guido Schmechel). Right answer, two round trips, and the merge was mine to perform.

This is not the paging complaint in another costume. Even with paging, "everything touching this person" still needs two calls and a hand-merge, and the merge is where a row goes missing: #89326 appears in one result set and not the other, and nothing in either answer signals that the other set exists. A caller who only thinks to ask reportedBy — the obvious reading of "issues von X" — silently misses what that person is on the hook for, and the answer looks complete.

## Query

Two calls where one was wanted: typo3_forge_lookup {"open":"oldest","reportedBy":"Frank Nägler","status":"all","limit":50}, then {"open":"stale","assignedTo":"Frank Nägler","status":"open","limit":50}. Task text: "finde mir bitte alle forge issues von Frank Nägler".

## Suggestion

An `involving` parameter, or a mode value on the existing pair, that ORs reported-by and assigned-to for one person and returns the union. Every row already carries `reportedBy` and `assignedTo`, so the caller can tell which side each row came in on for free once the sets are unioned — no new row shape needed.

Keep reportedBy and assignedTo as they are for the two narrow questions; the tool description is right that they answer different things. Add the union for the broad question, which is the one a user actually says out loud.
