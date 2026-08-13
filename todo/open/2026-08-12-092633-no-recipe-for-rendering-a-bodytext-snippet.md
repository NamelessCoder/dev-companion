# write down how a rendering change is proved in a throwaway functional test

**Serves:** feedback/2026-08-12-092633-no-recipe-for-rendering-a-bodytext-snippet.md
**Priority:** normal

Judged on 2026-08-14 as step 1a landing as a document — `D-KNW-071` carries the
evidence, the boundary and what the priority rests on. Write
`knowledge/documents/core/testing/` a page on making a rendering measurable:
which cObj renders a snippet through a `lib.` object, which operator form takes
multi-line markup, how output is forced out of a test that would otherwise print
nothing, and the targeted invocation. Establish every step against
`.checkouts/`, on all covered lines, and bind what does not hold on all of them
— the feedback's own claim that a leading `<` in a value is a reference is
contradicted by `LosslessTokenizer.php:432-451`, so what actually bit that
session is the first thing to find out. Then route to it from the scratch-probe
paragraph in `skills/typo3-core-patch-review/SKILL.md` and the throwaway-test
rules in `skills/typo3-core-issue-triage/SKILL.md`, and archive the feedback in
the same commit.
