# Name the compatibility guides where an audit passes

**Serves:** feedback/2026-08-19-094457-the-guides-list-reached-me-only-as-ids-in.md
**Priority:** normal
**Branch:** todo/the-guides-list-reached-me-only-as-ids-in
**Claimed:** 2026-08-21

Judged on 2026-08-21 as the ladder's step 2, delivery, and written up in
`D-GUI-012`: the two `extension/compatibility/` documents are named by no
intent, so `typo3_task_guide` answers an audit with an empty `guides`, and the
only files that name them — `skills/base.md` inside the deprecation sweep, and
`skills/typo3-extension-upgrade` — are both out of reach of a session auditing
under `typo3-extension-health`. Decide which of the two placements carries them
and write it: an entry in `knowledge/task-intents.json`, which recognizes the
work from the task text and reads no checkout, or a step in
`skills/typo3-extension-health`, which can state the condition the feedback asks
for because `typo3_project_describe` reports `coreConstraint` and the installed
version in one answer. Read both against what each owns today, the way
`D-GUI-012` read the shape it chose — and note that `TaskIntents::owned()`
withholds a guide from a brief that changes nothing unless the intent carries
`changesNothing`, so an intent that is to reach an audit carries it.
