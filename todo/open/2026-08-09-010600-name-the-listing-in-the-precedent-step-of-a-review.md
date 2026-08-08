# Name the listing in the precedent step of a core patch review

**Serves:** feedback/2026-08-08-224429-the-changelog-precedent-that-decided-the-review.md
**Priority:** normal

Judged as step 4, wording — `D-SKL-029`, which carries the evidence and the two
sessions it rests on. Rewrite the precedent bullet of
`skills/typo3-core-patch-review/SKILL.md` so `typo3_changelog_lookup` with
`type` and `version` and no query at all stands above the title-word query, with
the checkout below both rather than as the first fallback; say there that an
open major needs the `tag` bound as well, since 14 holds 36 Important and 99
Breaking entries against a `limit` that caps at 50. Establish before writing it
whether the Forge tracker states the change kind the commit keyword carries —
the step is to send a reviewer from a listed entry's issue number to
`typo3_forge_lookup`, and #106401 answering `Bug` against a `[BUGFIX]` commit is
one case rather than the rule. Extend
`SkillTest::aPrecedentIsAskedForInTheWordsAnEntryIsTitledIn` to hold the new
sentences, and archive the feedback in the same commit.
