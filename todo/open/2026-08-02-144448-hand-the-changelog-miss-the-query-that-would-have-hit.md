# Hand the changelog miss the query that would have hit

**Serves:** feedback/2026-07-31-194819-conformance-review-of-a-typo3-14-site-package.md
**Priority:** normal

Ladder step 4, wording, on the evidence in
[`D-ANS-016`](../../decisions/answers/ans-016-a-miss-names-the-query-that-would-have-hit.md):
the miss in `ChangelogLookup::answer()` prints the per-word reach and stops,
where `typo3_label_lookup` ends the same line with "ask again with the one that
narrows best". Add that sentence to the changelog miss, and above it name the
largest subset of the query terms that does reach entries, peeled a level at a
time — 28 ms over 3763 entries for a five-term query, against the 23 ms the
`tag` filter already costs. Decide in the same pass whether the peel belongs in
`LabelSearch`, which both tools go through, or in `ChangelogLookup` alone, and
carry `R-ANS-006` through: its statement covers this and its **Held by** names
the hint corpus only. What the two queries return is in the decision, so nothing
about them needs establishing again.
