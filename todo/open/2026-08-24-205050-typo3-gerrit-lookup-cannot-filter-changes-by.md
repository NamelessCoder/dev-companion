# typo3_gerrit_lookup cannot ask for the changes a person has not touched

**Serves:** feedback/2026-08-24-205050-typo3-gerrit-lookup-cannot-filter-changes-by.md
**Priority:** normal

Give `typo3_gerrit_lookup`'s `backlog` a person filter that excludes, beside the
`owner`, `reviewedBy` and `involving` that select. What it answers is "changes I
could review" — open work that is not mine and that I have not already voted on
— and `-owner:` with `-reviewedby:` is the query, verified anonymously against
review.typo3.org.

Settle the name first, which is the part the query is not: `AGENTS.md` does not
take a negation where an affirmative is meant, and every obvious spelling of
this argument is one. Decide it in `decisions/` beside `D-ANS-107`, whose
**Since then** says why this was left out of the first build rather than
forgotten.

This card is what is left of the feedback after 2026-08-25; the person, size and
vote filters it also asked for are built.
