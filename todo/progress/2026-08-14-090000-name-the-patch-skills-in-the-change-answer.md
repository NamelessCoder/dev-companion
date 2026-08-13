# Name the patch skills in the change answer

**Serves:** feedback/2026-08-12-092545-a-german-language-review-request-activated-no.md
**Priority:** normal
**Branch:** todo/name-the-patch-skills-in-the-change-answer
**Claimed:** 2026-08-13

Judged as `D-SKL-038`: step 2, delivery. The skill and the order it holds exist
and reached nothing, and the one call the session did make is the answer that
says a patch set exists. Give `GerritLookup::answer()` a third tail beside the
`git rev-parse HEAD` and the fetch sentences, on the `change` form alone: name
`typo3-core-patch-review` and `typo3-core-patch-checkout` as the two workflows a
patch set in front of a caller opens, and `typo3_project_describe` as the call
the order starts with. The `issue` form takes none of it, and neither does
`typo3_forge_lookup`. Two skill names in a rendered answer want the guard
`SkillTest::everySkillNamedInKnowledgeIsPublished` gives the ones in
`knowledge/`, which reads `task-intents.json` alone today — widen it or write
its sibling, so a renamed skill fails here rather than in somebody's project.
Priority is `normal` because two sessions reported the shape; what the tail buys
is unmeasured, which is the entry's first **Assumed**.
