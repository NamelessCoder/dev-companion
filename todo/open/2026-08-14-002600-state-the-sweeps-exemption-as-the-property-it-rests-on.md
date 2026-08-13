# State the sweep's exemption as the property it rests on

**Serves:** feedback/2026-08-11-055337-base-md-s-deprecation-sweep-is-owed-by-the.md
**Priority:** normal

`D-SKL-037` is the judgement, ladder step 4: step 5's exemption in
`skills/base.md` is written as three examples — a triage, a reproduction, a
review of a report — and the second read-only task shape to arrive, a core patch
review, was in none of them and skipped the sweep without saying so. Rewrite
that paragraph so what it states is the property, add that the exemption ends
where the workflow produces a change and that a report names the step it did not
reach, and carry the same sentences into `R-SKL-005` and into
`SkillTest::theDeprecationSweepIsSkippedOnlyWhereTheChangeTouchesNoTypo3Api`,
which asserts them verbatim. Archive the feedback in that commit.
