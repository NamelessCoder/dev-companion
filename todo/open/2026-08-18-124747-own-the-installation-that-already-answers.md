# Own the installation that already answers, in the skill that created it

**Serves:** D-SKL-059, feedback/2026-08-18-071526-task-guide-returns-two-installation-intents-but.md, feedback/2026-08-18-074606-no-skill-owns-an-installation-that-boots-but.md
**Priority:** normal

Write the half of `skills/typo3-development-installation/SKILL.md` that owns an
installation which already answers, and re-cut **Where this stops** so it stops
giving that half away. What the section covers is settled in `D-SKL-059`; what
it says is not, so read before writing it: `typo3_hint_lookup` for
`installation-boot`, `installation-exception-output` and the site-configuration
hints, and the two feedback call by call rather than by their conclusions. Two
things it owes beyond the order: a log that stayed empty is itself the finding
that separates a status code TYPO3 returned on purpose from an uncaught
exception, which is what `feedback/2026-08-18-074606` paid a wrong turn for; and
a first boot that writes a deprecation log crosses into
`typo3-extension-conformance`.
`SkillTest::anInstallationIsBuiltInDependencyOrderAndHandsOverOnceItAnswers`
asserts the closing heading verbatim, so it moves with the cut.
