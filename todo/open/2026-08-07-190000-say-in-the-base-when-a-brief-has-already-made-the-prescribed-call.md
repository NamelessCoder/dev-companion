# Say in the base when a brief has already made the prescribed call

**Serves:** feedback/2026-08-07-065259-task-guide-returning-a-hints-key-displaced-the.md
**Priority:** normal

`skills/base.md` step 4 prescribes `typo3_hint_lookup` for each subsystem in
scope and says a single broad query is not subsystem evidence. Step 3 already
carries the reverse clause — skip `typo3_task_guide` where this skill's own name
came out of that call — and step 4 has none, although the guide makes the same
matched call for the paths it is given. Measured on 2026-08-07 for the three
Extbase persistence paths of the feedback: the lookup returns the same three
hints at limit 4, 8 and 20, so the brief carried all of them and a separate call
would have returned nothing new. The session that skipped step 4 lost nothing,
and the rule says it should have. `typo3_task_guide` now states which of the two
happened — `HINTS_COMPLETE` where it carried everything, `HINTS_TRUNCATED` and
the ids where it did not (`R-GUI-009`) — so the base can name that sentence as
what discharges the step, and keep the call owed wherever the brief says it
stopped short. This is a skill contract and it reaches every published skill, so
it is the expensive kind: the copy in somebody else's project is not corrected
by the next release. Decide with it whether the feedback's other reading holds —
that the guide should return the calls still owed with their subsystem arguments
filled in — which for this call would have been an empty list.
