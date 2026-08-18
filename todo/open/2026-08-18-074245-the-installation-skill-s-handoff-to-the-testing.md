# Write the installation skill's testing crossing where it happens

**Serves:** D-SKL-053, feedback/2026-08-18-074245-the-installation-skill-s-handoff-to-the-testing.md
**Priority:** normal

Step 4. `R-SKL-018` was applied to the seven extension workflows and
`typo3-development-installation` is not one of them, so its crossing into
`typo3-extension-testing` is the last handover in `skills/` still standing in
the paragraph the workflow is being left in — which a session read on load and
did not act on when the task grew a test forty minutes later. Write it as a step
at its own moment, and close the two holes it went through in
`SkillTest::aSkillThatHandsOverSaysToInvokeTheSuccessor`: the `$crossings` map
names only `typo3-extension-conformance` for that skill, and the position half
reads the last paragraph for `nvoke` while this crossing says `activate`, which
is the verb `D-SKL-053` was written to falsify.

`feedback/2026-08-18-081159` is the same failure read as a property of skill
selection rather than of one crossing, and `feedback/2026-08-18-080630` is
`typo3-extension-upgrade`'s description; both keep their own cards.
