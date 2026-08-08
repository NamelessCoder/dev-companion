# Write the patch development handoff as a step

**Serves:** feedback/2026-08-08-224450-the-triage-handoff-fired-as-a-step-and-the.md
**Priority:** normal
**Branch:** todo/write-the-patch-development-handoff-as-a-step
**Claimed:** 2026-08-08

Ladder step 4, and the rule is already written: `R-SKL-018` says a skill that
hands over tells the session to invoke the next one, and
`typo3-core-patch-development` is the one crossing it was never applied to. Give
that skill a closing section in the form its two neighbours use — once the
checks pass and before the patch is handed over or pushed, invoke
`typo3-core-patch-review` on the diff and take its findings as the work list,
which the ownership paragraph already says to do with them. Keep that paragraph;
it is what states the boundary. Then add the skill to
`SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`, which today loops over
two skills against one hardcoded successor name and so cannot express a crossing
that runs the other way. `D-SKL-022` carries the evidence and the sweep that
says no other skill needs the same edit.
