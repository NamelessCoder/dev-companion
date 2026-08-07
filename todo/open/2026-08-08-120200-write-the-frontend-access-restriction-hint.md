# Write the frontend access restriction hint

**Serves:** feedback/2026-08-07-233501-typo3-hint-lookup-answered-three-core-frontend.md
**Priority:** high

Nothing in the corpus covers inherited frontend access restriction. Probed on
2026-08-08: of 129 hints only `content-element-preview` carries `fe_group` at
all, and none carries `extendToSubpages`, `groupAccessGranted` or
`accessGrantedForPageInRootLine`. Establish it against `.checkouts/` and write
it. What the reporting session read, as the place to start rather than the
conclusion: `RecordAccessVoter::groupAccessGranted` reads only the record's own
`fe_group` and never walks the rootline, returning true where it is empty;
`accessGrantedForPageInRootLine` is the rootline-aware form that honours
`extendToSubpages`; `PageInformationFactory` and
`PageRepository::getDescendantPageIdsRecursive` consume the second while
`PageLinkBuilder` consumes the first — which is why TYPO3 emits a link it then
refuses to serve with "Subsection was found and not accessible". Check each on
both sides of a version boundary and name the checkout in the commit. The
vocabulary matters as much as the statement: the changelog writes this subject
as "access restricted pages" and the error message says "subsection", while the
TCA column is `extendToSubpages`, and a caller arriving with any of the three
has to reach it.
