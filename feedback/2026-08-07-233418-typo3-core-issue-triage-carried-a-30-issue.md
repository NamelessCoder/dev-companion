---
date: 2026-08-07T23:34:18+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3-core-issue-triage carried a 30-issue backlog triage to a reproduced verdict without improvi...

## Observation

Task: find the 30 oldest unresolved issues in the core tracker, take the first genuine bug report, and establish against a 15.0.0-dev core checkout whether it still reproduces. My transcript begins at the user's request — there is no earlier summarised context, so this reports the whole session.

typo3-core-issue-triage was the only skill I activated, and it fitted without improvisation. Four of its instructions did concrete work:

- "The list is the first deliverable, and choosing from it is not yours", with the escape hatch for a request that really was "just find me something". The user's request delegated the choice explicitly ("take the first one that looks like a real bug"), so I handed over the 30 rows, named the one I took and why, and proceeded. Without that escape hatch I would have stopped to ask for a number the user had already told me to pick myself.

- "Read the count that comes back against the number of entries." typo3_forge_lookup returned total 1478 alongside a 30-row page, so I could state the slice honestly instead of implying the backlog was 30 issues.

- "It has to be seen failing before it is believed" and "a green that ran over no files is not a green". I ran the untouched functional test first (21 tests / 21 assertions, SUCCESS) before enabling the reproduction row, so the subsequent red was measured against a harness I had shown to inspect something. I would not have run that baseline otherwise.

- "Separate the three claims the report mixes." Issue 15984's reporter was right about the symptom and named a cause (class.tslib_menu.php calling checkPageGroupAccess) whose class has not existed for a decade. Reporting the issue invalid on that basis is exactly the failure the skill warns about, and the warning is what stopped me from doing it.

The checklist's verdict-first structure worked as designed: picking "Still happens" named the evidence it owed — code path at file and line, a failing test, the branch the reproduction ran on — and all three were deliverable. "The throwaway test comes out again" produced a clean tree at the end.

One thing to change: references/base.md step 5 prescribes a deprecation sweep of one call per declared major per tag, and its skip condition is written for a task that changes code ("skip only where the change touches no TYPO3 API"). A read-only triage of one issue matches neither side of that condition. Followed literally it would have cost roughly seven calls across ext:core, ext:frontend, TCA, TypoScript, Fluid, Backend and Frontend at major 15, none of which bear on one report. The triage skill's own step 5 — changelog lookup with the words the report uses — is the right instrument and is what I used instead.

base.md step 2 was accurate and saved a call: it anticipates that a core checkout reports no project-own extensions and says to state that rather than call typo3_extension_describe. typo3_project_describe returned extensions: [] and that is exactly what happened.

## Query

Session task: "Find the 30 oldest open unresolved issues in the TYPO3 Forge tracker, then take the first one that looks like a genuine bug report and establish against this core checkout whether it still reproduces, was fixed in the meantime, or was never a defect."

## Suggestion

Keep unchanged: the escape hatch on "choosing from it is not yours", the count-against-page rule, the seen-failing-first rule, and the three-claims separation. Each of the four changed what I did, and two of them changed the answer. In references/base.md, state that the deprecation sweep does not apply to a triage, or move the sweep out of the shared base into the skills that change code — its current skip condition has no branch a read-only triage falls under.
