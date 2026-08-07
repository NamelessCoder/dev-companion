---
date: 2026-08-07T23:35:24+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_forge_lookup returning notes and relations whole separated a feature filed as a Bug from a ...

## Observation

Task: 30 oldest unresolved core issues, then verify the first genuine bug report against a 15.0.0-dev checkout. Four typo3_forge_lookup calls carried most of this session, each one round trip, none needing a retry with different arguments.

What must not be broken later:

1. The open/oldest + tracker=Bug + limit=30 call returned the whole first deliverable in one round trip, with total 1478 beside the 30 rows and the project's full category list. The count is what let me report this as the oldest slice of 1478 rather than implying the backlog was 30 issues.

2. Notes returned whole. The user asked for "the first one that looks like a real bug". The oldest issue, 14858 from 2005, is filed as a Bug and reads like one from its subject line. Its description asks to "add the possibility", and its most recent note — Benni Mack, 2026-01-23 — concludes: "If this should be configurable, then we need a new option, making this not a bug, but actually a feature." Without that note I would have taken 14858 and reported a twenty-year-old feature request as a verified defect. One field changed the answer.

3. Notes and relations together. Issue 15984's notes carry Susanne Moog's 2012 reopening ("we had to revert the patch because of the massive performance impact it had"), and its relations carry precedes: 32756. Reading 32756 supplied the revert's numbers — 537 pages, 10,330 queries, a 1 GB memory limit — and Christian Kuhn's "The patch of #15984 is reverted now in all branches". That chain is the whole answer to what the user actually asked for, and it cost two calls.

4. Riccardo De Contardi's 2017 and 2020 notes on 15984 record full reproduction steps and observed output on 7.6, 9.5 and 10.4-dev, including the exact error text "Subsection was found and not accessible". They told me which surface to reproduce on and what the symptom looks like when it fires.

A payload that truncated notes, sampled or dropped older ones, or returned relations without them would leave this task unanswerable from the tracker. The decisive note on 14858 was the sixteenth of sixteen; the decisive note on 15984 was the twelfth of sixteen. Neither is near the top.

## Query

open: "oldest", tracker: "Bug", limit: 30 — then issue: "14858", issue: "15984", issue: "32756"

## Suggestion

Do not truncate or sample notes on an old issue, and do not decouple relations from them — both decisive notes in this session sat late in long note lists. Keep total alongside a limited page. Keep the category list in every answer; it is what makes a narrowing second call unnecessary.
