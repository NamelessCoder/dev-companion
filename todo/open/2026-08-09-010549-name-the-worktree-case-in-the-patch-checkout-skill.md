# Name the worktree case in the patch checkout skill

**Serves:** feedback/2026-08-08-224413-typo3-core-patch-checkout-stayed-shut-on-a.md
**Priority:** normal

Step 4, the wording: the description's step clause names the branch path alone,
so a request whose word was `worktree` read as some other skill's case
(`D-SKL-024`, confirmed 2026-08-09; the trim that wrote the clause back is
`D-SKL-026`'s third **Wrong if**). Rewrite the description in
`skills/typo3-core-patch-checkout/SKILL.md` so the worktree is one of the two
ways in and the steps go back to the body — cutting the clause pays for the
words, and the listing has 109 characters spare against the 3600
`SkillTest::everyDescriptionIsWrittenToTheBudgetTheyShare` allows — and give the
body the worktree path it then promises, established from what is already here
rather than recalled: `knowledge/test-suite-hints.json` carries the
`composerInstall` precondition of a fresh worktree and the `cgl` suite that
reads no file in one, and
`knowledge/documents/core/contribution/gerrit-workflow.md` has the worktree
section. Both halves are one edit to one contract, because a trigger that routes
a worktree task into a body with no worktree in it is the same failure one step
later.
