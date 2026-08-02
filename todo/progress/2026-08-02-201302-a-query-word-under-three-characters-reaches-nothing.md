# A query written in Fluid tags does not reach the Fluid book by name

**Serves:** feedback/2026-08-01-003000-underlying-failure-was-a-systemic-lack-of-fluid.md, R-DOC-003
**Priority:** normal
**Branch:** todo/a-query-word-under-three-characters-reaches-nothing
**Claimed:** 2026-08-02

The last of *a query word under three characters reaches nothing*, which was
three steps rather than two. The tokenizer was the first
([`D-ANS-028`](../../decisions/answers/ans-028-a-two-letter-query-word-is-searched-for-and-the-stopword-list-is-what-keeps-the-others-out.md)):
`TermSearch::terms()` admits a two-letter word, so `f:if` reaches
`Global/If.html` at all. The ranking constant was the second
([`D-ANS-032`](../../decisions/answers/ans-032-the-dilution-reference-of-the-manual-ranking-is-the-length-of-an-ordinary-title.md)):
`Documentation::UNDILUTED_WORDS` is 3 rather than 12, so a title is weighed by
its length and the page is fourth of ten rather than eighth.

Fourth is where those two stop, and the measurement says so rather than guesses
it. Three of the ten pages carrying `if` are titled `if` — the two TypoScript
function pages and this one — and `security.ifAuthenticated` is three words, so
all four are undiluted and score 198, and the order among them is the order the
index was built in. No dilution reference separates identical titles, and the
field weights cannot either: all four matched in `title`, so any title weight
scales all four alike — measured at `title` 4, 6 and 8, `path` 1, `manual` 4,
and the rank does not move. `f:or` and `f:then` do not get that far. Both words
are stopwords, so those queries have no term left at all.

What separates the three pages titled `if` is the book, and the query says which
book: `f:` is the Fluid namespace prefix, which
[`D-KNW-024`](../../decisions/knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
made a domain keyword for the hints and nothing reads for the manuals. So the
step is to give `Documentation` that route — the `manual` field of the Fluid
ViewHelper Reference is what the prefix should reach, the way `Domains::detect()`
routes a hint — and to measure it the same way: the 41 prompts of
`Scenarios::load()` and `::contracts()` through `Documentation::lookup()` at
14.3 with the manual roots fetched once, plus the seven queries this repository
already asserts an answer for, whose ranks `D-ANS-032` records to compare
against.

`f:or` and `f:then` need one thing more, and it is the open question: whether a
stopword can be admitted for one corpus and not another. `STOPWORDS` is shared,
so taking `or` and `then` out of it makes `Global/Or.html` a candidate for every
English "or" in the hints and the prose too. What would settle it is measuring
both — a per-corpus admitted list against the shared one — over
`bin/cli hints:probe`, `bin/cli hints:coverage` and the same 41 prompts. Nothing
here has needed a per-corpus stopword yet, so whether it is worth the concept is
the part to say out loud rather than assume.
