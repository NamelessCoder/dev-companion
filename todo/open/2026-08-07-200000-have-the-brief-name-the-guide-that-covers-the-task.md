# Have the brief name the guide that covers the task

**Serves:** feedback/2026-08-07-065313-server-scope-was-never-called-so-the-typo3.md, feedback/2026-08-07-132535-a-full-core-patch-review-finished-without-a.md
**Priority:** normal

The half of `R-ANS-028` that did not land. `typo3_rule_lookup`,
`typo3_test_run_guide` and `typo3_script_lookup` now hand the document over as a
`documentId` call, and `typo3_task_guide` still does not: it names the skill
that owns a task and says nothing about the procedure that covers it, although
it is the call a session makes at the moment it is deciding how to work. Both
feedback ask for the same thing in different words — name the applicable
`typo3://guides` document the way skills are already named, and consider naming
`typo3_server_scope` for a first session against this server. The mapping is the
open part and the reason this is queued rather than done with the rest:
`Documents::search()` over the task text is the matcher that already exists, and
what it needs is a threshold, because a guide named on a weak match is the
`alsoInHints` failure again — a pointer the caller learns to skip. Settle
whether the brief names a document at all where nothing matches well, since
saying nothing is a legitimate answer and a wrong guide is not. Two sessions
gave the same reason for never calling `typo3_server_scope`: orientation felt
complete because the brief had already routed them, which is the moment this
would speak.
