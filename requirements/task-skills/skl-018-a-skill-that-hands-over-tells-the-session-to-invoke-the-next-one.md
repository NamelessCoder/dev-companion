---
id: R-SKL-018
status: held
restsOn: [D-SKL-022]
---

# R-SKL-018 — A skill that hands over tells the session to invoke the next one

**Where a skill's work ends and another skill's begins, it says to invoke that
skill by name at the point the crossing happens.**

A closing paragraph about ownership is documentation of a boundary. It competes
with everything else in the window, and a session holding exactly the handoff it
describes reads it and carries on. What fires is an instruction at the moment
the trigger occurs.

## From

Three sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-065244`:
`typo3-core-issue-triage` activated, the session states it read the closing
paragraph, and it then wrote a full patch over roughly forty turns without
invoking `typo3-core-patch-development` — deciding the changelog obligation, the
suites, the databases, the trailers and the release branches for itself.
`feedback/2026-08-07-132559`: the same crossing out of
`typo3-core-patch-review`, which edited `ColumnMap.php`, wrote a functional
test, ran seven suites and amended the commit while still holding "it does not
change the patch". `feedback/2026-08-07-130022` is the description side —
`typo3-core-patch-checkout` covers rebasing, every noun around it is about a
change fetched from review, and a session asked to rebase its own commit
correctly did not open it.

## Held by

- `SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`
