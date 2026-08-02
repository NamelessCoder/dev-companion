# A query word under three characters reaches nothing

**Serves:** feedback/2026-08-01-003000-underlying-failure-was-a-systemic-lack-of-fluid.md
**Priority:** normal
**Branch:** todo/a-query-word-under-three-characters-reaches-nothing
**Claimed:** 2026-08-02

What `D-ANS-023` measured after indexing the ViewHelper reference and did not
act on. The book is in and `Global/If.html` is in it, and no query
naming `f:if` reaches it: `TermSearch::terms()` drops every word under three
characters and `then` is a stopword, so `f:if f:then f:else condition
ViewHelper` is searched as else, condit, view and helper, and answers with the
Else ViewHelper page instead. Read why the floor is three — the docblock on
`PREFIX_FROM_LENGTH` is about a different length and says what a short pattern
does to a corpus — and settle whether a short word can be admitted as a whole
word rather than as a prefix, which is what `TermSearch::carries()` already does
below four characters. Both corpora go through this: measure against the hints
with `bin/cli hints:probe` and against the manuals at `targetVersion: "14"`
before and after, because a two-letter term admitted everywhere also admits
`f:or`, `id`, `be` and `fe`, and the last two are a namespace prefix on a
hundred and eighty-nine page titles.
