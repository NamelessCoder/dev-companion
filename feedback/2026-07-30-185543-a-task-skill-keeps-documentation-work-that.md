---
date: 2026-07-30T18:55:43+02:00
category: tool-gap
status: open
tool: typo3_task_guide
---

# The backend-to-documentation hand-off still needs forward evidence

## Observation

The backend-module skill now stops after verified implementation, explicitly
activates `typo3-extension-documentation`, carries forward the extension scope
and verified public behavior, and states that extension functionality is
documented in the extension rather than the project around it. `SKILL-07`
exercises a task that has to cross that boundary.

**Trimmed 2026-08-01.** Half of this now has forward evidence, from the fourth
`REVIEW-01` run: a session routed its findings to the
`typo3-extension-documentation`, `typo3-content-element-development` and
`typo3-extension-testing` workflows **by name**, and kept conformance
responsible for re-checking them. Naming the next owner is no longer the open
part.

What remains is the crossing itself: no recorded run shows a session **activate**
the second skill and edit documentation inside it. A review stops at findings by
design, so `REVIEW-01` cannot establish this and no amount of re-running it will.

`SKILL-07` names the task shape, which since the 2026-07-31 split makes it a
contract case — read and pasted for inspection, never recorded as forward
evidence, because a prompt that names the route cannot prove an agent found it.
So this note needs either a forward review whose task genuinely crosses the
boundary, or the acceptance that the crossing is held by the contract case and
`SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition` and read by
hand. Decide which before running anything.
