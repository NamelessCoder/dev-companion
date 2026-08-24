# A CI run answers differently twice on one tree

**Serves:** tests/
**Priority:** normal
**Branch:** todo/a-ci-run-answers-differently-twice-on-one-tree
**Claimed:** 2026-08-24

`CoreFixtureTest::everyAnswerThatDoesNotMoveWithARootIsDerivedFromOne` failed
once on `typo3_rule_lookup` moving between roots on `rules: hit` and
`rules: miss`, then passed alone and in three later runs, two of them full
`composer ci` reporting different assertion counts on one tree — 66214 against
66225, seen on 2026-08-24 by the session working
`todo/i-dropped-a-sentence-from-a-changelog-rather`, whose diff touched nothing
the test reads. A differing count says the population is computed rather than
fixed, so the first step is to read what `CoreFixtureTest` enumerates and
whether anything below `.checkouts/` or a cache reaches it, then either remove
the dependency or state it with a test that fails the same way every time. It is
worth more than one intermittent: `bin/cli todo:home` runs `composer ci` before
every merge, so several sessions working at once each pay for it.
