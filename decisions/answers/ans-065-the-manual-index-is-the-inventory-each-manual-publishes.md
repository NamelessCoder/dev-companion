---
id: D-ANS-065
title: The manual index is the inventory each manual publishes
date: 2026-08-08
status: open
coveredBy:
  - DocumentationTest::aBodyThatIsNotAnInventoryIsNotAnIndex
  - DocumentationTest::aPageIsIndexedUnderTheTitleTheInventoryStates
  - DocumentationTest::anApiIdentifierReachesThePageThatIsNotNamedAfterIt
  - DocumentationTest::theNotFoundPageIsNotOneOfTheAnswers
---

# D-ANS-065 — The manual index is the inventory each manual publishes

**`Manual\Documentation` searches the `objects.inv` Sphinx writes beside every
manual, held under its `ETag` and revalidated, rather than the links of the
rendered root.**

What the root gave was the link text of a navigation tree, which is a theme's
markup and an abbreviation of the title: the page TYPO3 Explained calls "Assets
(CSS, JavaScript, Media)" was indexed as "Assets", and no question naming CSS or
JavaScript reached it.

## Evidence

- Measured against `docs.typo3.org` on 2026-08-08, over the four manuals
  `Documentation::DOCUMENTS` names at 14.3. Coverage: the rendered roots index
  1420 pages, the inventories 1431, 1416 of them shared. What only the root has
  is `singlehtml/Index.html`, one per manual, which `robots.txt` disallows for
  `User-agent: *`. What only the inventory has is each manual's own entry page,
  its `404.html`, and the pages its navigation omits.
- The titles disagree on **505 of the 1416 shared pages**. The ViewHelper
  reference agrees on 30 of 188: it navigates to `Global/If.html` as "if" and
  publishes it as "If ViewHelper <f:if>".
- The seven queries `D-ANS-032` ranked, re-ranked over both indexes at
  `UNDILUTED_WORDS` 3. The rank of the page each is committed to, root then
  inventory: `TCA inline …` 1 and 1;
  `Fluid AssetCollector css javascript ViewHelper` **12 and 2**;
  `FunctionalTestCase executeFrontendSubRequest …` **1 and 17**;
  `Record API Fluid template access record.header` **18 and 11**;
  `page title event` 1 and 1; `f:if` **2 and 1**;
  `f:if f:then f:else condition ViewHelper` **5 and 3**. Sum 40 against 36.
- The one that got worse is a chapter, not a miss.
  `Testing/FunctionalTesting/Index.html` is published as "Functional testing
  with the TYPO3 testing framework", seven words against the two of the link
  text, so dilution costs it the rank — and
  `Testing/FunctionalTesting/Introduction.html` of the same chapter is third.
- Wire size, compressed both ways: the TYPO3 Explained root is 19 951 B and its
  inventory 307 986 B; the four roots together are 41.6 kB against 494 kB of
  inventories. Revalidation answers **304 with a zero-byte body** on all four,
  in about 60 ms each.
- `objects.inv` answers 200 for all four manuals on all four covered versions —
  12.4, 13.4, 14.3 and main. Over the 5498 `std:doc` lines those sixteen
  inventories carry, not one abbreviates its URI with `$` or carries an anchor,
  and none abbreviates its display name to `-`. Three pages of the ViewHelper
  reference are published as `<Unknown>`.
- Compression was offered and never asked for. `Vary: Accept-Encoding` and
  `Content-Encoding: gzip` on every artefact tried, and the root that is 19.9 kB
  compressed is 169 693 B plain — so every manual lookup took 8.5 times the
  payload it had to.

## Decided

- `Http\Fetch` asks for compression on every read, and returns the `ETag` beside
  the status and the body. Both are one policy for every host this server reads,
  which is what that class is for.
- Only `std:doc` is read out of the inventory. The other roles are the objects
  inside the pages — 748 TCA properties at 14.3, and every label and section
  title — and what this searches is a table of contents (`R-DOC-001`).
- The index is held per URL with its entity tag and revalidated on every lookup.
  It is not held in `Http\Recent`, which holds an answer for a chosen while
  because its source cannot say whether it is still current; this source can, so
  there is no while to choose and nothing goes stale. That branch is measured
  and not tested: a transport is a body without a status or a tag, so a test
  that hands one in never holds an index and never revalidates one — the same
  gap `Http\Fetch` already names for the mapping of a status onto an answer.
- A body that is not an inventory is not an index. It is the same answer as a
  host that said nothing, which is `D-ANS-034` applied to an artefact that is
  not JSON: a 200 with a challenge page in it would otherwise empty the corpus
  and read like a search that found nothing.
- `404.html` is dropped. Sphinx renders the "content was removed" template as a
  document, so it is in every inventory, in no navigation tree, and titled in
  two words ordinary enough to be searched for.
- `UNDILUTED_WORDS` stays 3. Swept over the new corpus on the same seven
  queries, the sum of ranks is 31 at 1, 35 at 2, 36 at 3, 35 at 4, 36 at 5 and
  6, 39 at 8 and 49 at 12 — flat where the corpus is, and the reason for 3 is
  unchanged: a book name is two or three words and must not be diluted.

## Assumed

- That the inventory keeps being published beside every manual, in version 2 of
  the format. It is a build artefact of the same run that renders the pages, and
  Sphinx writes it for the cross-project references other manuals resolve
  through it.
- That the entity tag holds across representations. The host serves the same one
  for the compressed and the plain body and answered 304 for it, so asking for
  compression does not cost the revalidation.
- That a session asks more than once. The first lookup costs 494 kB against the
  41.6 kB the roots cost, and every later one costs nothing — the break-even
  against what this replaced is twelve lookups in a session.
- That the stated title is the better index term where the two disagree. Five of
  the seven queries say so and one says the opposite.

## Wrong if

- A manual stops publishing `objects.inv`, or publishes a format this cannot
  read. The book disappears from the search while its pages render fine, and the
  caller is told the source did not answer.
- A session that makes one lookup is the ordinary one. Then this costs the host
  ten times what the navigation tree cost and the holding never pays it back.
- The longer stated titles cost more ranks than they buy. The functional testing
  chapter already answers with a sibling page rather than its index, and a
  second query doing the same is the pattern rather than the exception.
