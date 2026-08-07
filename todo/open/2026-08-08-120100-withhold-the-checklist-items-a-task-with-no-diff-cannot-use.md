# Withhold the checklist items a task with no diff cannot use

**Serves:** feedback/2026-08-07-233443-typo3-task-guide-answered-a-core-issue-triage.md
**Priority:** normal

The second half of the same call. `changeType: "audit"` returns removal,
extension-scanner and changelog-file items — "enumerate what it removes or
renames before judging it", a matcher below
`typo3/sysext/install/Configuration/ExtensionScanner/Php/`, the `[!!!]` prefix,
`checkRst` over a core diff — to a task that produces no diff at all. The
reporting session used none of them and says so. The brief already withholds
hint sections by domain, so the mechanism exists; what it lacks is the reading
that an audit of a report is not an audit of a diff. Settle at the same time
whether `audit` is one change type or two: the session picked it because it is
documented as writing no file, then found it means reviewing a body of code
rather than reviewing a report against code, and says those need different
briefs. A third value, or `audit` branching on what the task names, are both on
the table and neither is decided.
