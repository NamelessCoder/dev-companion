---
date: 2026-08-19T13:13:36+00:00
category: tool-gap
status: closed
closed: 2026-08-19
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_forge_lookup cannot list issues by the person who reported them

## Observation

Task was: list every forge issue filed by a named person (Frank Nägler), a routine question when triaging one contributor's backlog or reviewing what a core team member still has open.

typo3_forge_lookup has no way to express it. The three modes are issue (one number), query (fulltext) and open (enumerate unresolved, narrowed by tracker/category/createdBefore/updatedBefore). None takes a person.

query="Frank Nägler" returned 9 issues with total=9. That result is not just incomplete, it is misleading: it matches issues where the string happens to occur in the subject, description or a comment, so it mixes issues he was assigned (#98515, #71249) with issues a third party merely mentioned him in (#71040, #62501, #32549) — and it misses the actual answer almost entirely. A caller who does not already know that query is fulltext will read total=9 as "he filed nine issues".

The real numbers, from the Redmine REST API directly: author_id=52 gives 621 issues (4 open, 590 closed, 27 rejected); assigned_to_id=52 gives 588 (1 open). So the tool's answer was off by roughly two orders of magnitude with no signal that it was.

The enumerated results the tool already returns carry an assignedTo name, so the person dimension is present in the output but not in the input — a caller can see it and cannot filter on it. Note also that the raw API exposes only numeric ids for author_id/assigned_to_id; there is no public users.json without admin rights, so resolving a display name to an id currently requires reading an arbitrary issue that person touched and lifting the id out of its author/assigned_to object. That resolution step is exactly the kind of thing the server is better placed to do once than every caller is to rediscover.

## Query

Task: "find all forge issues by Frank Nägler". Called typo3_forge_lookup with query="Frank Nägler", limit=25. Fell back to raw Redmine API via curl: GET https://forge.typo3.org/issues/98515.json to read assigned_to.id, then GET https://forge.typo3.org/issues.json?author_id=52&status_id=*&limit=100&offset=N&sort=id:asc paged seven times.

## Suggestion

Add a person filter to typo3_forge_lookup, taking a display name rather than a numeric id, and resolving the name to a Redmine user id inside the server (from a cached name→id map, or by reading it off a matching issue as a fallback). Two separate parameters, because they answer different questions: reportedBy (Redmine author_id) for "what has this person filed", and assignedTo (assigned_to_id) for "what is this person on the hook for". Both should narrow the open enumeration, and should also be usable on their own to enumerate a person's issues regardless of status — a status parameter (open / closed / all) alongside them would cover "what does this contributor still have open" and "what has this contributor filed over the years" with the same call.

Where a name is ambiguous or resolves to nothing, answer with the candidate names rather than an empty set, the way category already answers a word matching no category by listing the ones that exist.

Independently of that filter, the query mode should say in its answer that it is a fulltext search over subject, description and comments — so that a total of 9 for a person's name is not read as a count of that person's issues. The description of query does say "words to search the tracker for", but the failure mode when the words are a person's name is severe enough to be worth naming explicitly, e.g. "a person's name matches only where it appears in the text; use reportedBy/assignedTo to enumerate a person's issues".
