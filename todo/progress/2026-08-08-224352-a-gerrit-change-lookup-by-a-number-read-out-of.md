# Route the triage's previous-attempt step to the patch

**Serves:** feedback/2026-08-08-224352-a-gerrit-change-lookup-by-a-number-read-out-of.md
**Priority:** normal
**Branch:** todo/a-gerrit-change-lookup-by-a-number-read-out-of
**Claimed:** 2026-08-08

Judged as `D-SKL-028` on 2026-08-09, which carries the re-run and the reasoning.
Rewrite step 7 of `skills/typo3-core-issue-triage/SKILL.md`, which asks for a
`typo3_gerrit_lookup` call step 4 has already made: name the change form for a
`reviews` entry the step 4 answer did not carry, and route to
`typo3://guides/core/contribution/gerrit-workflow` for the fetch that gets the
attempt's diff, without writing a ref form into the skill. `typo3_rule_lookup`
is already in that skill's `ROUTING_SKILLS` order in `SkillTest` and first
occurs above step 7, so naming it there moves nothing. The refspec half of the
feedback is reported whole by `feedback/2026-08-08-224354`; this todo does not
build it, and whether the feedback closes depends on that half, which was not
read here.
