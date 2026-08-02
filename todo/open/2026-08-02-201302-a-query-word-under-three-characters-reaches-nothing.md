# The page titled after a two-letter tag is eighth of ten that score the same

**Serves:** feedback/2026-08-01-003000-underlying-failure-was-a-systemic-lack-of-fluid.md, R-DOC-003
**Priority:** normal

What is left of *a query word under three characters reaches nothing*, which
was two steps. The tokenizer is the first and is done —
[`D-ANS-028`](../../decisions/answers/ans-028-a-two-letter-query-word-is-searched-for-and-the-stopword-list-is-what-keeps-the-others-out.md):
`TermSearch::terms()` admits a two-letter word, the two-letter words that say
nothing are named in `STOPWORDS` instead, and `f:if` now reaches
`Global/If.html` where it used to reach nothing at all.

It reaches it eighth. Ten of the 1419 pages the four manuals index at 14.3
carry `if` as a whole word — two TypoScript function pages,
`security.ifAuthenticated`, `mfa.ifHasState`,
`ShouldUseCachedPageDataIfAvailableEvent` and the rest — and all ten score
exactly 198, so the order among them is the order the index was built in.
`f:if f:then f:else condition ViewHelper` puts it tenth of 204, and
`typo3_documentation_lookup` returns six.

The tie is `Documentation::UNDILUTED_WORDS`, which is 12. `TermSearch::score()`
weighs a term by how much other text it was found among, and no title in any of
the four books is twelve words long, so a page titled `if` and a page titled
Should Use Cached Page Data If Available Event are worth the same. Measured: at
a reference of 3 the page titled `if` is fourth for both queries — and the six
results of all 41 scenario prompts change, which is why it was not done beside
the tokenizer. So the step is to pick that reference against the whole prompt
corpus rather than against these two queries, the way `D-ANS-025` picked
against the hint corpus, and to say in a decision what moved. The field weights
are the other candidate and were not measured: `title` is 4 against `path` and
`manual` at 2.

`f:or` and `f:then` are the same miss by a different route and are not this
step. Both are stopwords, both name a page of that book, and taking them out of
`STOPWORDS` would make `Global/Or.html` a candidate for every English "or" —
the corpus is shared, so the hints and the prose pay for it too. What would
settle it is whether a stopword can be admitted for one corpus and not another,
which nothing here has needed yet.

Measure with the sweep the tokenizer step used: the 41 prompts of
`Typo3CmsMcp\Upkeep\Scenarios::load()` and `::contracts()` through
`Documentation::lookup()` at 14.3, the manual roots fetched once and cached,
`bin/cli hints:probe` and `bin/cli hints:coverage` for the corpus that must not
move.
