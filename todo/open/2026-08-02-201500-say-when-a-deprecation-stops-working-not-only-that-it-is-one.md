# Say when a deprecation stops working, not only that it is one

**Serves:** feedback/2026-07-31-194821-conformance-review-of-a-typo3-14-site-package-i.md
**Priority:** normal

Ladder step 2, delivery, on the evidence in
[`D-ANS-020`](../../decisions/answers/ans-020-a-deprecation-is-answered-by-the-version-that-removes-it.md):
the removal version is what an upgrade audit decides on, and
`ChangelogLookup::answer()` carries neither it nor the rule in `deprecated-apis`
that would supply it. Settle the shape first, because a per-entry field is empty
for 31 of the 75 deprecations of 14 and reads as "no removal planned" — the
silence-as-verdict failure `D-ANS-009` was built against. Three candidates: the
clause parsed out of the entry, the rule stated once in the closing paragraph,
or both with the entry overriding. Read the `@deprecated` annotation at the
trigger site as a fourth and more exact source before choosing — `#105297` is
where prose and annotation were checked against each other. Carry the answer
into the declared `outputSchema` where it becomes a field, since `R-ANS-002`
puts what a caller acts on in the data and not only in the text. The counts and
the two wordings already in `knowledge/` are in the decision, so nothing about
them needs establishing again.
