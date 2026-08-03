# Carry the changelog miss narrowing in the data, and name the corpus after it

**Serves:** feedback/2026-08-03-144349-task-review-core-commit-9f6c6eb9093-110359-the.md
**Priority:** normal

[`D-ANS-043`](../../decisions/answers/ans-043-a-miss-is-answered-in-data-and-says-which-corpus-its-silence-belongs-to.md),
which a review session paid for on 2026-08-03: it read `matchCount: 0` and the
five other fields, reported that nothing came back to re-ask with, and settled
its question by grep — while the text of the same answer offered
`typo3 backend entry point`, which returns the entry that review turned on. The
miss branch of `ChangelogLookup::answer()` already holds every value: `$counts`,
`$reached`, `$outside` and `$subsets`. Declare them in `outputSchema()` and fill
them beside `matchCount`, under the same two withholdings the text already
makes — no subsets where a `tag` was asked for, because a subset promises
entries the same call does not return, and the counts say whether they were
taken inside the narrowing or outside it. Then add one sentence after the subset
line, and only where a subset was offered and the caller may still come back
empty: a changelog records change events, so a mechanism nobody changed has no
entry, and what still holds in a version is `typo3_documentation_lookup` with
`targetVersion`. It goes after the re-query and not in place of it — the reason
is in that entry's **Decided** and in the new **Since then** on `D-ANS-010`.
`R-ANS-002` and `R-ANS-018` are the two requirements this puts on the miss path,
both `held` today by cases over other tools, so the cases that hold this one
belong beside `PackageSourcesTest::aMissNamesTheLargestPartOfTheQueryThatWouldHaveHit`
and are what the commit adds to the two files. Archive the feedback in that same
commit.
