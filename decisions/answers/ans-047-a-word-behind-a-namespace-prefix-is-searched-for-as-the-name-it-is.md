---
id: D-ANS-047
title: A word behind a namespace prefix is searched for as the name it is
date: 2026-08-03
status: open
coveredBy:
  - TermSearchTest::aWordBehindANamespacePrefixIsNotAStopword
  - TermSearchTest::theSameWordAfterTheColonOfASentenceIsNot
  - DocumentationTest::aTagNamedAfterAStopwordIsReachedByItsOwnName
---

# D-ANS-047 — A word behind a namespace prefix is searched for as the name it is

**`TermSearch` keeps a word written behind a namespace prefix as a term whatever
the stopword list says: the `or` of `f:or` names a ViewHelper.**

`f:or` and `f:then` came back empty. The tokenizer splits at the colon, the
prefix falls under `MIN_LENGTH` and the name that is left is in `STOPWORDS` for
what it does in a sentence — so a query naming a tag reached the scoring with no
term at all. `D-ANS-036` had already routed such a query to the book that
documents the tags; there was nothing left for the book to rank.

## Evidence

- Measured over the four manual roots at 14.3, fetched once on 2026-08-03, the
  same corpus `D-ANS-032` and `D-ANS-036` were measured on. `f:or` returns
  `empty` before and `Global/Or.html` **first** after; `f:then` returns `empty`
  before and `Global/Then.html` **first** after. Each is the only page of the
  1419 that carries its term as a whole word, so each answer is one result.
- `f:if` does not move: the same six pages in the same order.
- `f:if f:then f:else condition ViewHelper` does. `Global/Then.html` arrives
  **second** and `Global/If.html` falls from 4th to 5th — the number `D-ANS-036`
  recorded. All six are pages of the book the query is routed to before and
  after, so what changed is the order inside it and no page of another manual is
  involved.
- The other five of `D-ANS-032`'s seven rank identically, page for page.
- Nothing else can move, and that is a property of the rule rather than a
  sample: it reads the character in front of the word, so a query carrying no
  `<identifier>:<word>` takes the path it took before. Over the 168 query-shaped
  texts this repository holds — the 3 forward and 38 contract prompts of
  `Scenarios::load()` and `::contracts()`, and 127 hint titles — exactly one
  carries such a colon at all: `EXT:form` in the form-framework hint title,
  whose `form` is not in the list.
- The per-corpus admitted list this was queued as was measured first, so nobody
  runs it again. Both words taken out of the shared list moves the hint corpus
  not at all — all 162 texts of that day reach the same domains and the same
  first three hints — and costs **seven of the 41 prompts a page of their six**,
  every one of them on the English conjunction: *anything an editor or a visitor
  could reach*, *fix that, then take me through pushing it for review*. The
  corpus the shared list was feared for is the one nothing happens to.

## Decided

- The rule is on the tokenizer and reads the query's own syntax. What admits the
  word is how it is written, not which corpus is being searched and not which
  book the query was routed to.
- No list of namespaces. `Installation\FluidNamespaces` is one, and it is the
  installation's — `Search/` stands under no installation and is reached when
  there is none. An extension's prefix is declared per template anyway
  (`D-ANS-036`), so no global list would carry it.
- No parameter on `TermSearch::terms()`, which is what the todo proposed. It
  would have one caller passing one value, and it would put the choice of words
  in `Documentation::book()` — the routing deciding the tokenizing.
- The colon has to touch both sides. A sentence puts a space after it, which is
  what keeps "then" a stopword in *fix that, then take me through pushing it for
  review*.
- The prefix itself stays out. One and two letters fall under `MIN_LENGTH`, and
  `be:` under the stopword list that already holds it — which is every namespace
  the corpora carry.
- `Documentation::book()` is untouched and still reads `f:` alone. Which book a
  query is searched in and which words it is searched by are two questions.

## Assumed

- A colon with no space around it is a qualified name. That is the shape of a
  tag namespace, of `EXT:`, and of nothing this repository has seen written in
  prose.
- The local name is what the caller means. `f:or` is read as a question about
  the ViewHelper called "or" and never as a question about disjunction.
- The 168 texts are the corpus of real phrasings. None of them is written in
  Fluid tags, which is what `D-ANS-036` assumed for the same reason and is the
  reason this costs them nothing.

## Wrong if

- `EXT:core` and `EXT:typo3` admit a word the list holds for a reason. Both are
  extension keys, so the reading is the intended one, and a word half the corpus
  carries weighs near nothing — but such a query is now scored on one term more
  than before, and its coverage is measured against it.
- A tight colon turns up in prose after all. `note:the` in a generated line, a
  label written without the space, and the word behind it is a term.
- The tie inside the book is what callers notice. `Global/If.html` is 5th rather
  than 4th on the four-tag query, behind the `then` page that arrived — a caller
  asking about a condition in four tags is answered with one of them.
- A tag is named after a word that is a stopword **and** common in the titles of
  another book. `or` and `then` each carry exactly one page today; a third such
  tag whose word is spread over the corpus would be admitted on the same rule
  and reach the spread instead.
