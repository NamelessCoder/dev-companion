---
id: R-SKL-022
title: 'A review surface names the lookup that can answer it'
status: held
restsOn: [D-SKL-030]
heldBy:
  - SkillTest::aReviewSurfaceNamesTheLookupThatCanAnswerIt
  - SkillTest::everySkillRoutesThroughTheOwnersOfItsOwnFactsInOrder
---

# R-SKL-022 — A review surface names the lookup that can answer it

**A review surface a tool can answer names that tool, at the surface.**

A checklist item is answered in the report, so it is the one place a session
cannot pass by. Naming the lookup there is what turns a surface into a call; a
surface that lists only the half somebody already thought of is disposed of from
whatever the reader brought with them. The documentation surface was named for
two things and listed one, and every item under it was the changelog's.

## From

A review of Gerrit change 95179 on 2026-08-08 (`feedback/2026-08-08-224516`),
which shipped the claim that the wording for `stdWrap.override` lives outside
the core repository, offered as the reason no documentation change was owed.
`typo3_documentation_lookup` answers that query and its first result is the page
the patch makes false. The session never called it, and nothing led there:
`typo3-core-patch-review` sent the obligations a diff raises to
`typo3_rule_lookup` and the precedent to `typo3_changelog_lookup` and named no
third owner.
