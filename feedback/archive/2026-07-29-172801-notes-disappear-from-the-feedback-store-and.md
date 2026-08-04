---
date: 2026-07-29T17:28:01+00:00
category: bug
status: closed
closed: 2026-07-29
commit: 23cab9e
subject: "[FEATURE] Answer what became of a note that was worked off"
tool: typo3_feedback_record, typo3_feedback_list
directory: /home/benji/projects/site-new
---

# NOTES DISAPPEAR FROM THE FEEDBACK STORE, AND status="all" DOES NOT RETURN THEM.

## Observation

NOTES DISAPPEAR FROM THE FEEDBACK STORE, AND status="all" DOES NOT RETURN THEM.

Measured, not assumed. Over two sessions I recorded 17 notes. Checking the store twice, hours apart:

- First check: typo3_feedback_list status="all" returned 1. The nine notes of the previous session were gone from feedback/ on disk.
- Second check, after recording eight more: it returned 3. Five of those eight were gone, including four recorded within the preceding twenty minutes.

The content is not being lost — that part is good, and I verified it hint by hint rather than assuming:

- excludeDoktypes replacing the menu default → now in frontend-page-rendering, expanded past what I wrote
- the icon registry being backend-only → now the FIRST hint of icon-usage, exactly the placement I asked for
- ext_emconf.php and the composer.json metadata → three new hints in extension-files, including the " - " title split
- the whole project test harness note → a new hint of its own, project-extension-tests, with all seven points

So an ingest reads the notes, folds them into the knowledge base, and removes the files. That works, and it works well.

The problem is what it costs:

1. There is no record of what was reported. After the fact I cannot answer "did I report this, and what happened to it" without re-deriving every finding and probing the knowledge base for each one — which is what I just spent several tool calls doing, and only because a human asked me to.

2. Anything the ingest could not act on vanishes silently. One of the five that disappeared was the tool-gap asking for "is extension X part of the core, and since when" — it needs a code change to typo3_project_scope rather than a knowledge entry, nothing about the server changed, and the note is gone. That request has now been made twice and dropped twice. The third time I will have no way of knowing it was ever made.

3. status="all" promises otherwise. The parameter documents itself as "all: every recorded note", and the enum open/all implies a closed state exists. A closed note showing up as closed would solve both points above; a deleted note cannot.

This is also a self-referential risk for every note I have written: I report a gap, the note is consumed, and if the fix does not happen there is nothing left that says it was ever asked for.

## Query

typo3_feedback_list status="all" — returns 3 notes; I recorded 8 in this session and 9 in the one before it

## Suggestion

Do not delete an ingested note — mark it. status="closed" or "incorporated", with a pointer to the hint it became, and let status="all" return it. That makes typo3_feedback_list answer "what did I report and what came of it", which is the question a caller actually has, and it stops requests that need code rather than prose from falling out of the system entirely. If the deletion is a deliberate archive step, an archive/ directory next to feedback/ and a status filter that reaches it would do the same job.
