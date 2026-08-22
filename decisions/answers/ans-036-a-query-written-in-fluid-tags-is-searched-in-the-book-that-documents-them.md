---
id: D-ANS-036
title: A query written in Fluid tags is searched in the book that documents them
date: 2026-08-03
status: open
coveredBy:
  - DocumentationTest::aQueryWrittenInFluidTagsIsAnsweredFromTheFluidBook
  - DocumentationTest::aQueryIsRoutedToABookOnlyWhileThatBookAnswers
---

# D-ANS-036 — A query written in Fluid tags is searched in the book that documents them

**`f:` selects the Fluid ViewHelper Reference as the manual a query is searched
in, the way `Domains::detect()` routes a hint to a category.**

Ten pages of the corpus carry `if` and every one of them carries it in the
title. Three are titled `if` — the two TypoScript function pages and
`Global/If.html` — and `security.ifAuthenticated` is three words, so all four
are undiluted, all four matched the same field, and neither the dilution
reference nor a field weight separates them
([`D-ANS-032`](ans-032-the-dilution-reference-of-the-manual-ranking-is-the-length-of-an-ordinary-title.md)).
What separates them is the book, and the query says which book in the prefix
[`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
already made a domain keyword for the hints.

## Evidence

- Measured over the four manual roots at 14.3, fetched once on 2026-08-03. The
  corpus is what `D-ANS-032` measured on 2026-08-02 — 1419 pages, 10 carrying
  `if`, four of them undiluted — and its seven queries rank the expected page 1,
  12, 1, 18, 1, 4, 4 before this change, which is what it recorded. The two
  measurements compare.
- `f:if` ranks `Global/If.html` **4th before and 2nd after**, and its six are
  pages of the ViewHelper reference rather than the two TypoScript pages
  followed by four of them.
- `f:if f:then f:else condition ViewHelper` keeps it 4th and loses the two pages
  of other books from its six: `TranslateViewHelper.html` of TYPO3 Explained,
  which is one of the two answers
  [`D-ANS-023`](ans-023-a-viewhelper-question-is-answered-by-widening-the-index.md)
  was written from, and `UsingSettingTSconfig/Conditions.html`.
- Nothing else moves. The other five of the seven rank identically, and over the
  41 scenario prompts of `Scenarios::load()` and `::contracts()` **not one
  changes its six or its first hit** — none of them is written in Fluid tags.
- Naming the book as a query word was the other shape and is measured. Appending
  "Fluid" to a query carrying the prefix lifts `f:if` from 4th to 2nd as well,
  and it lifts every page whose *title* carries the word by the title weight of
  4 while the book's own pages gain the manual weight of 2: on the longer query
  `TranslateViewHelper.html` rose from 5th to 3rd and `Global/If.html` fell from
  4th to 5th. The word is not the book.
- What the route does not reach is the tie inside the book. Its
  `security.ifAuthenticated` is three words and so undiluted too, and it stands
  ahead of `Global/If.html` in the order the index was built in. Both are in the
  book the query names.

## Decided

- The route selects the candidates rather than weighing them, which is how a
  hint is routed — `fluid.json` is filtered out before a hint is scored. The
  term weights stay over the whole corpus, because what a term is worth is how
  few of all the pages there are carry it.
- Only `f:`. `be:` and `core:` are the two-letter risk `D-KNW-024` left out for
  the hints, and an extension's prefix is declared per template rather than
  globally.
- Only a book that answered. The route is in front of the scoring, so a root
  that is down would otherwise leave such a query with no candidates and report
  `empty` — "no match" for a reason the caller cannot see. What said how many
  roots answered now says which ones did, so this costs no concept.
- The tool description says it, because it is the one thing the answer does not
  show: the way to the Fluid chapters of the other manuals is to ask without the
  prefix.

## Assumed

- A caller writing `f:` wants the book that documents the tags. `D-KNW-024`
  assumed the same for the hints, where both candidates sat in one category and
  the assumption only decided which won; here the books are different.
- The seven pairs and the 41 prompts are the corpus this is measured on. No
  prompt is written in Fluid tags, so what the route costs a real phrasing is
  measured on two queries this repository wrote itself.

## Wrong if

- A question about writing a ViewHelper carries `f:` and loses TYPO3 Explained.
  `Developing a custom ViewHelper` is that manual's page and the ViewHelper
  reference has none, so "my own f:myTag renders nothing" is now searched in a
  book that documents every tag except that one.
- One tag named inside a question about something else takes the whole query
  with it. `f:` is enough on its own, so "the f:image in my FLUIDTEMPLATE setup"
  is answered without TypoScript Explained.
- The tie inside the book is what callers actually notice. `f:if` is 2nd rather
  than 1st, and the page above it is a different ViewHelper.

## Since then

The half this left open is built and is
[`D-ANS-047`](ans-047-a-word-behind-a-namespace-prefix-is-searched-for-as-the-name-it-is.md):
a query naming a tag whose name is in `TermSearch::STOPWORDS` reached the route
with no term for the book to rank, so `f:or` and `f:then` were routed and came
back empty. One number recorded above moved with it, measured on the same roots
at 14.3 on 2026-08-03: `f:if f:then f:else condition ViewHelper` now ranks
`Global/If.html` 5th rather than 4th, behind the `Global/Then.html` its second
tag reaches. Its six are pages of this book before and after, and `f:if` alone
is unchanged.
