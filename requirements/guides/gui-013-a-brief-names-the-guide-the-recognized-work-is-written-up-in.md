---
id: R-GUI-013
title: 'A brief names the guide the recognized work is written up in'
status: held
restsOn: [D-GUI-012]
---

# R-GUI-013 — A brief names the guide the recognized work is written up in

**Where a knowledge document is the write-up of the work `typo3_task_guide`
recognized, the brief names it, as the `typo3_rule_lookup` call with
`documentId` that reads it.**

The corpus is otherwise named once per session, in the `guides` key of
`typo3_project_describe`, which is the call a session makes before it knows what
the work is. A session whose client renders no resource list has no second way
in, so a page that arrives before the work arrives is one nobody goes back for.
The pointer is a name and not the page: a brief is one call inside a procedure,
and inlining the procedure into it would replace the reading it stands for.

## From

`feedback/2026-08-18-074226`. The session read the guides list while diagnosing
a 404, turned to test work three user turns later, and finished having added
functional tests without `extension/testing/phpunit` and verified a rendering in
a browser without `any/testing/browser-check`. Measured in this worktree the
same day, the brief for that test work named `core/contribution/rules` — the
page a core patch is judged by, handed to somebody's package.

## Held by

- `HintsTest::aBriefNamesTheGuideTheWorkIsWrittenUpIn`
- `KnowledgeTest::everyGuideAnIntentNamesIsADocument`
- `KnowledgeTest::aGuideNamedOutsideTheCoreIsNotTheCoresOwn`
