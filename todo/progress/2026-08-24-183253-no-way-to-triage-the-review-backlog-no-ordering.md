# Enumerate the open review backlog on typo3_gerrit_lookup

**Serves:** feedback/2026-08-24-183253-no-way-to-triage-the-review-backlog-no-ordering.md, feedback/2026-08-24-205050-typo3-gerrit-lookup-cannot-filter-changes-by.md
**Priority:** normal
**Branch:** todo/no-way-to-triage-the-review-backlog-no-ordering
**Claimed:** 2026-08-25

Build what `D-ANS-107` decided: a way into `typo3_gerrit_lookup` beside `query`
and `path` that enumerates the open changes of `Packages/TYPO3.CMS`, ordered
here by age and narrowed by size, vote state, mergeable, branch and person —
`delta:`, `label:`, `is:mergeable`, `age:`, `owner:` and `reviewedby:`, each
composed into the query by `Gerrit` rather than passed through — and widen
`Gerrit::change_()` so every row carries `insertions`, `deletions`, `mergeable`,
`created`, `unresolved_comment_count` and the label states of `submit_records`,
all six of which the review server already sends and this server drops. Settle
the argument's name first: `open` is a boolean narrowing `query` and `path`
today, so the enumeration cannot take the sibling tool's `"oldest" | "stale"`
spelling on that argument without breaking a caller passing `true`.
