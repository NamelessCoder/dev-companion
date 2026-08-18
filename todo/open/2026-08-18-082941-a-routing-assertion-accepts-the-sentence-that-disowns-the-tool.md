# Make a routing assertion refuse the mention that disowns the tool

**Serves:** tests/
**Priority:** low

`SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder` asserts a
routing by finding the tool's name anywhere in `SKILL.md`, with `strpos`. A
skill that names a tool in order to say the call is already answered satisfies
that assertion while telling the caller the opposite.

It happened once. `73cff0ab` rewrote `typo3-development-installation`'s step to
say `typo3_server_scope` is discharged by the base's `typo3_project_describe`
(`D-ANS-083`), and `ROUTING_SKILLS` went on listing the tool first for that
skill. Nothing failed, and `everySkillStartsFromTheBaseBeforeItsOwnEvidence`
took that same sentence as the skill's first routing. The list is corrected and
`theInstallationSkillNamesTheScopeCallOnlyToDischargeIt` holds this one skill on
the count of mentions, which is what a second routing would break.

What is open is the general form: what distinguishes a routing mention from a
disowning one in the text, for a skill nobody has written yet. A count per skill
does not generalise, because a tool legitimately routed to twice is ordinary.
