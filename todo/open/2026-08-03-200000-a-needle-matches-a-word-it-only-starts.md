# A needle that only starts a word matches it, and now it routes on it

**Serves:** decisions/
**Priority:** normal

`Text::containsWord()` anchors a needle at a word boundary on the left and
nowhere on the right, so `test` matches "testimonials". That is deliberate for
`deprecat`, which is a stem and matches every form the word takes. It is wrong
for `test`, and `feedback/2026-08-01-003356`'s own task is the case: "build a
testimonials content element with a custom backend preview" is recognized as
test coverage, and since
[`D-SKL-013`](../../decisions/task-skills/skl-013-the-guide-names-the-skill-that-owns-the-task.md)
it names `typo3-extension-testing` ahead of the skill that owns it.

It was a false intent before it was a false route: that task has carried the
test checklist and `typo3_test_run_guide` since the intent was written. What the
route adds is that a caller acts on it — loading a whole workflow is not the
same cost as reading two checklist items past.

The reading has not been done. What it turns on is whether a needle that has to
match whole is a property of the needle or of the corpus: `Hints`, `Documents`
and the label search go through the same matcher, and a stem like `deprecat`
proves the loose side is load-bearing somewhere. Establish which needles in
`knowledge/task-intents.json` are stems and which are words before changing the
matcher, and check `knowledge/hints/` for the same question — a rule matched on
a word it only starts is the same defect one layer down, where nothing routes on
it and nobody would notice.

`HintsTest::aBriefNamesTheSkillThatOwnsTheWork` is where the route is held, and
it asserts the skill that owns the work is named rather than that it is named
alone, so the fix has an assertion to tighten in the same commit.
