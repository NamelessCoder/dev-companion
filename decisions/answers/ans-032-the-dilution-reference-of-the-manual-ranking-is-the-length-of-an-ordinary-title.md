---
id: D-ANS-032
date: 2026-08-02
status: open
---

# D-ANS-032 — The dilution reference of the manual ranking is the length of an ordinary title

**`Documentation::UNDILUTED_WORDS` is 3, the ordinary field length of the manual
corpus, rather than 12, which is its longest title.**

`TermSearch::score()` weighs a term by how much other text it was found among,
and 12 was the value at which no title in the corpus is other text at all. So a
page titled after its subject and a page whose title is a long event class name
were worth the same for the one word they share — and the class name wins every
tie it is in, because it carries five more words to be found by. That is the
crowding dilution exists to answer, switched off by the constant that was
supposed to apply it.

## Evidence

- What the corpus's fields actually are, over the 1419 pages the four manuals
  index at 14.3: a title is 2.66 words on the mean, 2 on the median and 12 at
  its longest; a path is 7.16 and 18; a manual is 2 words for three of the books
  and 3 for the Fluid ViewHelper Reference. 12 was the maximum rather than the
  ordinary, which is why the docblock could say nothing here is long enough to
  be diluted and be right.
- The seven queries this repository has already committed to an answer for,
  ranked over the live index rather than over the stub the unit tests carry.
  Rank of the expected page at 12 and at 3: `TCA inline …` → IRRE / inline 1 and
  1; `Fluid AssetCollector css javascript ViewHelper` → Assets **17 and 12**;
  `FunctionalTestCase executeFrontendSubRequest …` → Functional tests **3 and
  1**; `Record API Fluid template access record.header` → Record objects **23
  and 18**; `page title event` → Page title API 1 and 1; `f:if` →
  `Global/If.html` **8 and 4**; `f:if f:then f:else condition ViewHelper` →
  `Global/If.html` **10 and 4**. Not one of them is worse at 3.
- The same sum of ranks across the range, which is flat above the corpus and
  falls into it: 63 at 24, 16 and 12, 62 at 10, 61 at 8, 59 at 5, 51 at 4, **41
  at 3**, 32 at 2, 33 at 1. 12 is already dilution switched off — 14, 16, 20 and
  30 move 3 of the 43 queries between them and nothing above 16 moves anything.
- What it costs on the 41 scenario prompts, the only corpus of real phrasings
  this repository has, each asked alone at 14.3 for six results: **all 43
  queries change their six, 20 change their first hit**, and 104 entries leave a
  top-6 answer while 104 arrive. It is not a marginal change and it is not
  presented as one.
- What arrives, read one prompt at a time. `CORE-01` is a DataHandler bug and
  gained `DataHandler` and `DataHandler basics`, neither of which was in its six;
  `CORE-04` deprecates `GeneralUtility::getUrl()` and gained Deprecation
  Handling; `CORE-05` is a functional test that fails locally and passes in CI
  and gained CI/CD Automation. What leaves is mostly event class names —
  `AfterPageUrlsForSiteForRedirectIntegrityHaveBeenCollectedEvent`,
  `ShouldUseCachedPageDataIfAvailableEvent`, five `ModifyRecordList*Event` pages.
- The counter that says the opposite, because it was run first and not
  following it is a decision. The mean number of query words reaching a returned
  page falls from 2.00 at 12 to 1.72 at 3, and results reached by one word or
  none rise from 79 of 258 to 114. That instrument counts a long class name as
  the better answer for the words it happens to contain, which is the failure
  being fixed rather than a measure of it.
- What it does not buy, which is the question it was picked for. `Global/If.html`
  is fourth and not first: three of the ten pages carrying `if` are titled `if`
  — `Guide/TypoScriptFunctions/If/Index.html`, `Functions/If.html` and this one —
  and `security.ifAuthenticated` is three words, so all four are undiluted, all
  four score 198, and the order among them is the order the index was built in.
  No length reference separates identical titles. What separates them is the
  book, and the query says which book in the `f:` prefix that
  [`D-KNW-024`](../knowledge/knw-024-the-fluid-namespace-prefix-is-what-a-template-question-is-written-in.md)
  made a domain keyword for the hints and nothing reads for the manuals.
- The field weights, which were the other candidate and are now measured. All
  ten pages carrying `if` carry it in `title`, so a title weight scales all ten
  alike: the page titled `if` stands at 8 of 10 at reference 12 and 4 of 10 at
  reference 3 under `title` 4, 6 and 8, under `path` 1, and under `manual` 4.
  Raising `title` to 8 makes the longer ViewHelper query worse — 4 to 8.

## Decided

- 3 rather than 2, which scores marginally better on the seven. Three of the
  four books are named in two words and the Fluid ViewHelper Reference in three,
  so at 2 its 189 pages are the only ones whose `manual` field is diluted and
  the books are weighed against each other by the length of their names. 3 is
  also where the corpus's own mean title lands.
- Not the field weights, on the measurement above. They cannot reach this at
  all: the tie is between pages that all matched the same field.
- The four books stay weighed as they are, and `path` stays at 2. A path is 7.16
  words against a reference of 3, so it is now diluted on nearly every page —
  that is the intended reading of a section name against a title, and it is what
  moves the deep-path event pages down.

## Assumed

- The seven pairs are ground truth. They are what this repository asserted
  before this change and they were not chosen for it, but they are seven, and
  four of them are ViewHelper or Fluid queries. A regression on a subject none
  of them names would not show here.
- A long title is a worse answer for the word it shares. That is what dilution
  says everywhere else in this server, and on this corpus it is nearly the same
  claim as "a class name is a worse answer than a page name", which is narrower
  than it sounds — every one of those pages is a real page about a real event.
- Measured against 14.3 as it was published on 2026-08-02, from the four manual
  roots fetched once. The ranking is over a table of contents, so a book that
  retitles its pages moves this without anything here changing.

## Wrong if

- A caller naming an event class stops reaching its page. `EXT-04` asks for a
  backend module listing records and lost all five `ModifyRecordList*Event`
  pages from its six; that is the trade this makes, and it is wrong if the class
  name is what people actually search by.
- A short title wins on being short. 114 of the 258 results over the 41 prompts
  are now reached by one word of the query or none, against 79 before, and the
  mean words reaching a result fell to 1.72 — the counter above is the one that
  would show this first.
- A fifth manual arrives whose name is four words or more. Every page of it is
  then diluted on the `manual` field against every page of the other four, on
  every query that names a book.
- The hint or prose corpora are found to want the same change. They have their
  own references at 200 and 400 and are not touched here, and a shared value
  would be the mistake this one is: the manual's fields are titles, and theirs
  are bodies.

## Covered by

- `DocumentationTest::aPageTitledAfterItsSubjectOutranksALongerTitleThatAlsoCarriesTheWord`
