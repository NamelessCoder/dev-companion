# Give the order that writes a core patch the assessment it starts with

**Serves:** feedback/2026-08-02-145128-task-assess-forge-105403-and-fix-it-collecting.md
**Priority:** normal
**Branch:** todo/task-assess-forge-105403-and-fix-it-collecting
**Claimed:** 2026-08-03

Judged on 2026-08-03 as `D-SKL-010`: delivery, and the venue is the skill rather
than `typo3_task_guide`. Write the assessment step of
`skills/typo3-core-patch-development/SKILL.md` the way
`typo3-core-patch-review` already has it — `typo3_forge_lookup` with the issue
number, read for the status and target version as they stand today, the
relations, and the notes; `typo3_gerrit_lookup` with the same number before any
code is written, with the empty answer read as `D-ANS-033` says. Add the three
rungs the order does not carry: a deferred decision is checked against today's
API before its blocker counts, the argument that carries a bugfix is the same
inconsistency inside one version, and the blast radius is established while
assessing because it decides the change type. What that section owes before a
line of it is written is `documentation/clients/writing-a-skill.md`, and the
mirror of `SkillTest::aReviewReadsTheReviewThePatchIsAlreadyIn` is what would
hold it. The priority is `normal` because four sessions reported the two
lookups by hand — `feedback/2026-08-02-144511`, `144848`, `145217`, `145230` —
and the three rungs come from this feedback alone.
