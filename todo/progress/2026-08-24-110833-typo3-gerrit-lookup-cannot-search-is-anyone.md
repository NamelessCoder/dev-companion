# Search the review server by words and by path

**Serves:** feedback/2026-08-24-110833-typo3-gerrit-lookup-cannot-search-is-anyone.md
**Priority:** normal
**Branch:** todo/typo3-gerrit-lookup-cannot-search-is-anyone
**Claimed:** 2026-08-24

Add `query` and `path` to `typo3_gerrit_lookup` as a third alternative beside
`issue` and `change`, with `open` narrowing the search to what is under review:
`Gerrit` composes them into one query — the words as bare terms, the path as
`file:^<path>` — and fills each hit to the boundary `D-ANS-100` sets, which is
the one an issue search already answers in. The measurements that entry rests on
are the operators, so what is left to establish is what a hit costs at that
boundary and how an empty answer is qualified: a search reaching nothing carries
the private-change caveat `D-ANS-033` states for the issue direction, and
`indistinguishable` is where it goes.
