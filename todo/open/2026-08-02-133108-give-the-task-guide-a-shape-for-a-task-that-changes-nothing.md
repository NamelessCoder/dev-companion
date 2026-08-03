# Give `typo3_task_guide` a shape for a task that changes nothing

**Serves:** feedback/2026-07-31-194826-after-loading-the-typo3-extension-conformance.md, R-GUI-006
**Priority:** low

Ladder step 1b, the shape half: the brief is composed and there is no way to ask
for it in the form a review needs, so an audit gets the patch checklist. Read
first what a review brief would carry that the change one does not —
`skills/typo3-extension-conformance/references/checklist.md` is the written
account of what an audit does here, and `TaskGuide::CHANGE_TYPE_CHECKLIST` with
the five generic items above it is what a change one carries. Then place the
shape: an audit entry in `knowledge/task-intents.json`, a value on the
`changeType` enum, or both — the enum is a declared schema, so
`ToolContractTest` is written in the same commit. This does not turn on the
question the two waiting cards for the same property carry: `typo3_task_guide`
is reachable without a skill, and a caller who arrives that way gets the same
wrong-shaped brief.
