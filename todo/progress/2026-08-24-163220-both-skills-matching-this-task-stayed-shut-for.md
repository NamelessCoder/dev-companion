# Name the backlog job in the triage skill's description

**Serves:** D-SKL-076, feedback/2026-08-24-163220-both-skills-matching-this-task-stayed-shut-for.md
**Priority:** normal
**Branch:** todo/both-skills-matching-this-task-stayed-shut-for
**Claimed:** 2026-08-25

Write the backlog job back into `skills/typo3-core-issue-triage/SKILL.md`'s
description — picking an issue out of the backlog, in the words a user types for
it — and hold the pair with a test beside
`SkillTest::aWorktreeTaskMatchesTheSkillThatOwnsTheCheckout`: the words in the
description, and the "Find the candidates" section that has to answer once they
did. The description is at 192 characters of the 360
`SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn` allows. Then put the
rule into `documentation/contributing/writing-a-skill.rst` beside `D-SKL-024`'s,
read the eight skills nobody has checked for a second job, and archive the
feedback with `bin/cli feedback:archive` in the same commit.
