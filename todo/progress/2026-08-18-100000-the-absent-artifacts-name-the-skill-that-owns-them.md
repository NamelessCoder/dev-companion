# The absent artifacts name the skill that owns them

**Serves:** feedback/2026-08-17-213027-three-published-skills-were-named-by-name-in-a.md
**Priority:** normal
**Branch:** todo/the-absent-artifacts-name-the-skill-that-owns-them
**Claimed:** 2026-08-18

Name the owner beside each absence the `Ships:` line of `ExtensionDescribe`
renders — `typo3-extension-documentation` where `manual` or `readme` is null,
`typo3-extension-testing` where `tests` is empty — and only where the artifact
is absent, so an extension shipping all three answers as it does today.
`GerritLookup::workflow()` is the shape a tool answer names a skill in,
`SkillTest::everySkillNamedByAToolIsPublished` is what holds the two names
published, and the contract test needs the case with the artifacts and the case
without. `D-SKL-053` has the reading, why conformance is not among the three,
and what the answer may not turn into. It serves the same feedback as
`2026-08-18-100100`, so whichever of the two lands second archives it.
