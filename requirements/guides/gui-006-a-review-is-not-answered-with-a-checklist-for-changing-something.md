---
id: R-GUI-006
status: open
---

# R-GUI-006 — A review is not answered with a checklist for changing something

**A brief for a task that changes nothing does not tell the caller to keep the
patch focused, add test coverage and write the commit message.**

`typo3_task_guide` composes its checklist from `changeType` and the intents in
`knowledge/task-intents.json`. Both enumerate kinds of change — the enum offers
bugfix, feature, cleanup, test, documentation and `unknown`, and none of the
eleven intents is an audit — so a review falls through to the generic patch
checklist. Those items are not redundant beside a review's own workflow; they
are steps a review may not take, and the tool has no value a caller can state
the difference with.

## From

A conformance review of a site package in `site-new` that ran the call and
reported it added little for a pure audit
(`feedback/2026-07-31-194826`, 2026-07-31). Re-run on 2026-08-02 with `task="review
the TYPO3 project and site package"` and `changeType=unknown`: no intent matches,
and the checklist that comes back is "Keep the patch focused on the stated task",
"Add or update the narrowest useful test coverage" and "Write the commit message
with typo3_commit_message_guide".

## Held by

Nothing: the shape this demands does not exist yet.
