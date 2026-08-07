# Give every published skill an intent the brief can reach

**Serves:** feedback/2026-08-07-233443-typo3-task-guide-answered-a-core-issue-triage.md, R-SKL-019
**Priority:** high

`typo3-core-issue-triage`, `typo3-core-patch-checkout` and
`typo3-extension-documentation` are named by no entry in
`knowledge/task-intents.json`, so `typo3_task_guide` cannot route to them. A
session that described its task as "Triage an old open core bug report" was
answered `skills: ["typo3-extension-conformance"]` — reproduced on 2026-08-08
with its own arguments — because `audit` is the nearest intent that matches
read-only work, and its `skill` is the extension conformance workflow. Triage
has vocabulary of its own that nothing matches on today: tracker, backlog, Forge
number, issue report, "is this still a thing". Read the other two before
assuming they need the same shape — a checkout and a manual may belong on
existing entries rather than on new ones. **Write the check in the same
commit**, because that is what turns `R-SKL-019` from a sentence into something
nobody can regress: every directory under `skills/` is named by at least one
intent. If one of the three turns out to be deliberately unroutable, the check
gains a named exemption and the reason, rather than the entry being dropped.
`D-SKL-023` carries the judgement.
