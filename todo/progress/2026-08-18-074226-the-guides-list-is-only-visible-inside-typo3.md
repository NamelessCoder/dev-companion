# The guides list is only visible inside typo3_project_describe, so a session can finish without re...

**Serves:** feedback/2026-08-18-074226-the-guides-list-is-only-visible-inside-typo3.md
**Priority:** normal
**Branch:** todo/the-guides-list-is-only-visible-inside-typo3
**Claimed:** 2026-08-18

Judged on 2026-08-18 as the ladder's step 2, delivery, and written up in
`D-GUI-012`. The guides are here, the reporting client rendered no resource
list, and the only answer that names one is the orientation call. Measured the
same day: `typo3_task_guide` for a testing task on an extension path names the
`typo3-extension-testing` skill and `core/contribution/rules`, and nothing under
`any/`, `extension/` or `project/` can reach a brief at all, because
`TaskIntents::RULE_DOCUMENTS` is three core-contribution files.

The step is to have the brief name the guide the recognized work belongs to,
beside the skill it already names, as the `typo3_rule_lookup` call with
`documentId` that reads it. Establish first which of two shapes carries it —
widening the searched documents per scope, or a guide named on the intent in
`knowledge/task-intents.json` beside `skill` and `skillCore` — read against what
each intent owns today. It lands in the `rules` field of the output schema, and
the feedback stays open until the commit that implements it archives it.
