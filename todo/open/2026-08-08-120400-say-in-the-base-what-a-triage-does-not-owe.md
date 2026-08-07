# Say in the base what a triage does not owe, and how a session learns what exists

**Serves:** feedback/2026-08-07-233512-the-session-never-learned-typo3-server-scope-or.md, feedback/2026-08-07-233418-typo3-core-issue-triage-carried-a-30-issue.md
**Priority:** normal

`references/base.md` fixes the order every task starts in, and a session that
followed it faithfully reports both halves of what a fixed order costs.

What is in it fires where it should not. Step 5 prescribes a deprecation sweep,
one call per declared major per tag, and its skip condition is written for work
that changes code — "skip only where the change touches no TYPO3 API". A
read-only triage of one report matches neither side of that, and followed
literally it would have cost roughly seven calls across ext:core, ext:frontend,
TCA, TypoScript, Fluid, Backend and Frontend, none bearing on the issue. Say
that the sweep does not apply to a task that changes nothing, or move it out of
the shared base into the skills that do change code.

What is not in it never happens. `typo3_server_scope` is in no step, so the
session never called it and never learned the `typo3://guides` resources exist —
it says it did not know until it re-read the instructions while writing the
debrief. That is the fourth session on this subject and the first to name the
mechanism: the order is followed literally, so a step is the reliable way in.
`D-ANS-061` decided the lever is the tool a session does call, and
`todo/open/2026-08-08-090300` puts the inventory on `typo3_project_describe`,
which is already step 1 — read that card first, because if it lands this one may
need nothing. What the feedback offers instead is naming the guides by title in
the instructions, which reaches every client rather than only one that lists
resources; price it against `R-ANS-013`.
