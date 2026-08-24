# Name the calls the core review hands the patch workflow

**Serves:** feedback/2026-08-24-100534-steps-both-skills-prescribe-that-i-skipped.md, D-SKL-072
**Priority:** normal

Judged as step 2 of the ladder, delivery: the order exists and is correct, and
nothing the caller reads names it at the crossing where it has to run a second
time. Write the three calls into *Where the review ends and the rework begins*
in `skills/typo3-core-patch-review/SKILL.md` — `typo3_task_guide` with the
change type about to be written, `typo3_hint_lookup` for the paths about to be
edited, and the deprecation sweep — as calls rather than as the order to
restart, and add the test that holds them under the rules in
`documentation/contributing/writing-a-skill.rst`.
