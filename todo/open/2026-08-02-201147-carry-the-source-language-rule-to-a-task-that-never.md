# Carry the source-language rule to a task that never names a label

**Serves:** feedback/2026-08-01-003313-violated-the-language-file-rules-by-writing.md, R-ANS-015
**Priority:** low

Step 2 of the ladder, on the two probes in
[`D-ANS-024`](../../decisions/answers/ans-024-a-rule-reaches-only-the-task-that-already-names-its-subject.md):
the rule is here and only a query already naming labels reaches it.

Settle which answer carries it before writing anything, because the three
candidates are three different claims about who a rule belongs to — the answer
text of `typo3_label_lookup`, a line in the `content-elements` hint, or the
words the `labels` intent matches on. Establish what a session actually calls
between deciding to add an element and writing its first unit; the recorded runs
under `scenarios/` and the debriefs in `feedback/archive/` are where that is
readable. Then place it once, and leave `R-ANS-015` naming what holds it.
