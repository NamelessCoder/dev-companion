---
date: 2026-08-24T22:52:43+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# Both patch skills handle a linear task; this one grew scope eight times inside one session

## Observation

Task as given: "bitte review mir 93177". What it became, in order, each turn driven by a new user message that arrived while tool calls were in flight: review the patch -> amend it -> write the Gerrit comment -> write replies for four unresolved threads -> build a follow-up hardening patch -> extend it to the client side -> produce browser screenshots -> redo the screenshots properly -> rewrite the client fix twice -> write a changelog entry -> delete the changelog entry.

Both skills fitted the part they own and I would activate both again.

typo3-core-patch-review fitted well. Its instruction to establish the patch before reading the diff is what made me call typo3_gerrit_lookup second, which caught that the checkout was not the revision under review. Its dropped-candidate discipline is what made me disprove rather than dismiss: I nearly reported an admin regression from an early `return false` and instead read BackendUserAuthentication::check() and the groupData default array, which disproved it. Its scratch-probe permission is what turned "the tests presumably cover this" into two pasted runs — 4 failures without the fix, and a swapped predicate making the discriminating test fail. Keep all three.

The crossing to typo3-core-patch-development fired correctly. The user wrote "kannst du ihn fertigstellen das er backgeportet werden kann?" and I invoked the patch skill rather than carrying on under review rules. That warning is written as a step and it worked as one.

What neither skill carries is the shape this session actually had: scope that keeps growing after the crossing. Three concrete costs:

1. I built a client-side error UI (a wizard teardown, then a faked Close button) and removed both again after the user's screenshots showed the wizard chrome disappearing. The right answer was to unswallow the errors in four steps so the error branches the steps already declared could fire. Two rounds of work discarded.

2. I wrote an Important changelog entry, matched it against precedent, ran checkRst — and then deleted it, because the criterion is "requires manual action", not "is a hardening". I had read that exact rule hours earlier via typo3_rule_lookup and reasoned from precedent instead when the moment came.

3. I re-derived scope four times: is the hardening one patch or two, does it reach 13.4, does it need the client, does it need an entry.

The patch-development skill says "Keep the patch one change. What else you noticed is another issue and another patch." That is the right rule and it fired — but only for the first split. It has nothing for the second, third and fourth, where the user is actively pushing scope outward and the honest answer each time was a different one.

## Query

Skill(typo3-core-patch-review, args="93177"); later Skill(typo3-core-patch-development, args="Patch 93177 amendieren..."). Session ran across roughly 20 user turns, 12 of them arriving mid-tool-call.

## Suggestion

One paragraph in typo3-core-patch-development, next to "Keep the patch one change", for the case where scope grows after the crossing rather than before it:

"Where the request widens after the patch is under way, re-establish three things before writing code, and say which changed: what the change is (bugfix, task, hardening), which branches it reaches, and what it therefore owes. Each of those can flip on a widening and none of them is re-derived by carrying on. A change that gains a second subsystem — PHP plus the built frontend, say — has gained a build step, a lint suite and a backport constraint with it."

And, specifically for what cost me the most: before writing a new error path in the backend's TypeScript, check whether the component already declares one. Four of five localization wizard steps had an `error:` render branch with its own XLF label, all unreachable because the step's own task caught and returned an empty list. I wrote a parallel error surface next to a correct one that was switched off. A line in a hint — "a backend Lit step that renders a Task usually declares error:; make the task reject rather than adding a second error path" — would have found it in one reading.
