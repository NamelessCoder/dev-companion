# Hand the guide over in the answer that names it

**Serves:** feedback/2026-08-07-132535-a-full-core-patch-review-finished-without-a.md, feedback/2026-08-07-130058-script-lookup-carries-the-scripts-guide-but.md, feedback/2026-08-07-065313-server-scope-was-never-called-so-the-typo3.md, R-ANS-028
**Priority:** high

Make the three tools that already name a document deliver it:
`typo3_rule_lookup` returns a `uri` and nothing in the answer presents it as a
next action, and two sessions read a matched section without ever fetching the
document it was cut from. The strongest of the three offers is the one to price
first — let `typo3_rule_lookup` take a `documentId` and return the whole
document — because it works for a client that renders no MCP resources at all,
which is the client all three reports came from. Beside it sit the two cheaper
ones: have `typo3_test_run_guide` name `typo3://guides/core/testing/scripts` the
way `typo3_task_guide` names a skill, since the moment a session is about to run
something is the one moment it is certainly looking, and mark the `uri` in a
rule answer as readable whole rather than leaving it the least prominent field.
Do not start from `typo3_server_scope`: two sessions gave the same reason for
never calling it — orientation already felt complete — so a tool nobody invokes
is not the lever. `bin/cli hints:coverage` is the sweep that measures whether
the guides get reached, and `D-ANS-061` is the judgement behind this.
