---
date: 2026-08-07T23:34:43+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3_task_guide answered a core issue triage with a patch-review checklist and routed it to typo...

## Observation

Task: triage the oldest unresolved bugs in the core tracker against a 15.0.0-dev core checkout. references/base.md step 3 requires typo3_task_guide where the skill was not reached from that call, which was my case — I invoked typo3-core-issue-triage from its description.

It classified scope "core" correctly and its testSuites and options blocks were genuinely useful. Two parts were wrong for the task I described:

1. skills: ["typo3-extension-conformance"]. I had described the task as "Triage an old open core bug report", in a checkout that typo3_project_describe had reported one call earlier as kind "core-checkout" with extensions []. typo3-core-issue-triage exists, owns exactly this task, and was not named. The extension conformance skill is for reviewing an extension or sitepackage repository, which is not something a core checkout with zero project-own extensions can be doing.

2. The checklist returned is patch-review content, not triage content: "enumerate what it removes or renames before judging it", "hold each removal in a core patch to what it owes — a matcher below typo3/sysext/install/Configuration/ExtensionScanner/Php/, a Breaking or Deprecation changelog file its restFiles names, the [!!!] prefix", "run checkRst and checkExtensionScannerRst over a core diff that removes something". A triage writes no diff and removes nothing. I used none of those items.

I chose changeType "audit" because it is documented as writing no file and getting "what a review needs" — which is the closest of the available values. But "audit" appears to mean reviewing a body of code, whereas a triage reviews a report against code. Those need different briefs, and there is no changeType that says so.

The call was not wasted, because the suite and option blocks paid for it. But its routing field pointed away from the skill that owns the work, and a session that trusted that field over the skill list would have run an extension conformance review inside a core checkout.

## Query

task: "Triage an old open core bug report: establish whether it still reproduces against this checkout", changeType: "audit", targetVersion: "15", paths: [] — no paths yet, the issue had not been chosen. Called immediately after typo3_project_describe returned kind "core-checkout", typo3Version "15.0.0-dev", extensions [].

## Suggestion

Route a task whose text names a core issue, the tracker, a Forge number, or triage — in a checkout reported as kind "core-checkout" — to typo3-core-issue-triage. Either add a changeType for triage, or make "audit" branch on scope, since an audit of a core checkout with no project-own extensions is not an extension conformance review. Withhold the removal, extension-scanner and changelog-file checklist items where the task produces no diff, the same way the hint sections are already withheld by domain.
