# Say when a changelog match is somebody else's subject

**Serves:** feedback/2026-08-07-233553-a-changelog-query-for-the-tca-column.md
**Priority:** low

`typo3_changelog_lookup` for `extendToSubpages` — the TCA column name and the
natural word for the concept — answers with one entry: a 12.0 Breaking removing
an Indexed Search option that happens to spell it. The answer is arguably
correct, and the reporting session says so plainly: the frontend's inherited
access-restriction behaviour was never reworked, and a changelog records change
events, so an untouched area has no entry. Its two better-worded queries,
`showAccessRestrictedPages` and `typolinkLinkAccessRestrictedPages`, both
answered well.

The finding is what a session that started from the column name and stopped
there would conclude: one Indexed Search hit read as evidence about the area.
Say in the answer where the only matches come from a system extension unrelated
to the query's subject, rather than returning it flat beside nothing. The
alternative the feedback offers is bigger and worth pricing separately: relate a
TCA column to the vocabulary the changelog writes it in — here "access
restricted pages" and "subsection" — the way an identifier query already reaches
entries titled otherwise. Left at `low` because nothing was lost: the session
had already run the queries that answered.
