# A hints key in an answer may not discharge the call that is owed

**Serves:** feedback/2026-08-07-065259-task-guide-returning-a-hints-key-displaced-the.md
**Priority:** high

`typo3_task_guide` returns a populated `hints` array, and one session read that
as step 4 of `typo3-core-issue-triage`'s base already answered and never called
`typo3_hint_lookup` at all — in a session where the per-subsystem call was the
one that might have carried the fact the task turned on. The base prescribes the
call once per subsystem with concrete paths and says explicitly that a single
broad query is not subsystem evidence, so the substitution is against a rule
that is already written. Decide which of the two the session named: have
`typo3_task_guide` return the `typo3_hint_lookup` calls still owed with their
subsystem arguments filled in, so the next step is a call rather than a reading,
or label the `hints` key in the payload as a preview that discharges nothing.
The first is the one this run would recommend, because the second leaves a
plausible-looking answer in the place the step used to be. The failure is silent
from the server's side — it sees a `task_guide` call and no `hint_lookup` call
and cannot tell that its own answer is why — so whatever is chosen, say in the
todo that closes this what would now make it visible.
