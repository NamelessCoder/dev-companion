# Say which words emptied a rule lookup, rather than the core boundary

**Serves:** feedback/2026-08-01-115115-in-the-same-session-typo3-project-scope.md
**Priority:** normal

Judged as
[`D-ANS-037`](../../decisions/answers/ans-037-a-compound-rule-query-is-owed-the-section-its-score-prefers-and-a-miss-that-names-the-words.md),
step 4 of the ladder: `RuleLookup::answer()` reaches `noMatch()` only where the
prose, the hints and the withheld documents are all empty, so a query that
matched hints and no section prints `No section that holds outside the core
matched "<query>"` whatever the scope — with `scope: core` and
`withheldDocuments: []` from `/home/benji/projects/typo3-cms`, which is a
boundary that withheld nothing. Give that path the miss answer instead: the
document topics `noMatch()` already lists, and the largest part of the query
that would have reached a section, which `LabelSearch::largestReachingSubsets()`
computes for `typo3_changelog_lookup` under
[`D-ANS-016`](../../decisions/answers/ans-016-a-miss-names-the-query-that-would-have-hit.md).
Keep the outside-core sentence where `withheldDocuments` is not empty, which is
the only case it is true in. Hold it with assertions on both miss paths — no
test in `tests/` reaches either today — and name them in `R-ANS-006`, which this
tool is otherwise held to by `noMatch()` alone.
