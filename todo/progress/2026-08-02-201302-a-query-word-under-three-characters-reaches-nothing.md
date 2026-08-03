# `f:or` and `f:then` have no term left to search for

**Serves:** feedback/2026-08-01-003000-underlying-failure-was-a-systemic-lack-of-fluid.md, R-DOC-003
**Priority:** normal
**Branch:** todo/a-query-word-under-three-characters-reaches-nothing
**Claimed:** 2026-08-03
**Waiting on:** is a word that is a stopword everywhere else worth admitting for
    the one query that names the book titling a page after it? The per-corpus
    admitted list this todo proposed is measured below and is not what the
    evidence points at; what it points at instead is a `TermSearch::terms()`
    that takes the words one query admits, which is a parameter on the tokenizer
    all three corpora call. Putting this back in the queue unbuilt is one of the
    answers, and so is deciding that two queries do not buy the concept.

The book half is done and is
[`D-ANS-036`](../../decisions/answers/ans-036-a-query-written-in-fluid-tags-is-searched-in-the-book-that-documents-them.md):
`f:` selects the Fluid ViewHelper Reference as the manual a query is searched
in, so `Global/If.html` is second of the ten pages carrying `if` rather than
fourth and the two TypoScript pages titled `if` are no longer candidates for a
query written in Fluid tags. What is left is the two queries that never reach
the scoring at all: `or` and `then` are in `TermSearch::STOPWORDS`, so `f:or`
and `f:then` have no term and come back empty.

What the measurement showed, so nobody runs it again. Both words taken out of
the shared list, read over the 41 scenario prompts of `Scenarios::load()` and
`::contracts()`, the 121 hint titles, and the four manual roots at 14.3 fetched
once on 2026-08-03:

- The hint corpus does not move. All 162 texts reach the same domains and the
  same first three hints through `Hints::find()` before and after, because
  neither word is in an `appliesTo` pattern or a hint title.
- The manual corpus does, both ways. `f:or` reaches `Global/Or.html` and
  `f:then` reaches `Global/Then.html`, each first and each from nothing — and
  **seven of the 41 prompts lose a page of their six** to one of those two, on
  the English conjunction: *anything an editor or a visitor could reach*, *fix
  that, then take me through pushing it for review*.
- So the corpus is not what decides, which is what the proposed shape assumed.
  The corpus that goes wrong is the one being admitted for, and the hints — the
  corpus the shared list was feared for — are the ones nothing happens to.

What separates those seven prompts from `f:or` is that one query names the book
and the other says "or" in a sentence, and `Documentation::book()` now reads
exactly that signal. Every query that moved above carries `f:` or is one of the
seven, and no scenario prompt carries `f:`, so admitting a word only for a query
that is routed to the book costs the seven nothing. That is the shape to build
if the concept is wanted, and the concept is a parameter on the one tokenizer
the prose, hint and manual corpora share.
