---
id: D-ANS-096
title: An outside source is read in the form it publishes
date: 2026-08-23
status: confirmed
coveredBy: []
---

# D-ANS-096 — An outside source is read in the form it publishes

**A host that publishes an API is read through `Fetch::decode()` and nothing
else; the manual, which publishes none, is read out of its rendered pages.**

`D-ANS-034` said the lookups that reach a host read JSON and nothing else. That
described `Contribution/`, and it was already untrue of the fourth source when
it was written.

## Evidence

- Three of the four sources that reach a host publish JSON: the tracker
  (`Contribution\Forge`), the review server (`Contribution\Gerrit`) and the
  registry (`Publication\Ter`). All three go through `Http\Fetch::decode()`,
  which takes the XSSI guard off where there is one, decodes, and answers null
  for anything that is not an array.
- The fourth publishes none. `docs.typo3.org` serves rendered Sphinx pages, and
  what it publishes beside them is `objects.inv` — the inventory `D-ANS-065`
  made the index. The bodies are read with `DOMXPath` in `Manual\Documentation`
  and have been since `7d29c77a` on 2026-07-30, three days before `D-ANS-034`.
- What a reader is for is what a wrong guess costs. The tracker's protection
  answers 200 with a 7.5 kB HTML challenge page, measured on 2026-08-03, and
  `decode()` is what turns that into "the question was not answered" instead of
  into an answer.
- The answer shape does not divide the four. `status` is `answered`, `empty` or
  `unavailable` on all of them and `Result\Unreachable` carries the causes,
  which is `D-ANS-007`'s and is untouched here.

## Decided

- `Fetch::decode()` is the one JSON reader and every source with an API asks it.
  A parser is written only where the source publishes nothing else, which today
  is the manual and nothing beside it.
- A new source is judged on what it publishes rather than on how much it is
  wanted. Where the answer would have to be read out of a page nobody maintains
  as a document, the recipe belongs in `knowledge/` and the reading stays with
  the caller — which is what `D-ANS-034` decided and this keeps.
- `coveredBy: []`. What a test can hold is each reader, and `DocumentationTest`
  and `ForgeTest` do; which reader a source gets is a judgement made once per
  source, and no failure can catch it being made wrongly.

## Assumed

- That a rendered manual stays readable. The four books are built by one
  toolchain, and the reader takes the article and the inventory rather than a
  theme's navigation — which is the markup that did move, and `D-ANS-065` is
  where that was measured.

## Wrong if

- `docs.typo3.org` changes what it renders and the reader answers with half a
  page, or with a navigation tree, rather than with nothing. `DocumentationTest`
  reads fixtures rather than the site, so the first report of that would be a
  session's.
- A second source without an API turns out to be worth a parser. Two make "the
  manual" an exception list rather than an exception, and the rule then has to
  say what makes a page readable instead of naming one host.

## Confirmed on 2026-08-28

Both **Wrong if**s were read and neither has happened.

The manual still renders what the reader takes. `typo3_documentation_lookup`
asked `queries: ["site set"]` at `targetVersion 14.3` on 2026-08-28 answers
`answered` with six pages, each carrying an excerpt of the article's own prose —
the PSR-14 event page opens on what the event is fired after — rather than a
navigation tree or half a page. That is the first **Wrong if** watching for the
theme moving under `Manual\Documentation`, and the reading is a live one, which
`DocumentationTest` is not.

No second source without an API has arrived. The groups that reach a host are
the same four: `Contribution\Forge`, `Contribution\Gerrit`, `Publication\Ter`
through `Fetch::decode()`, and `Manual\` out of the rendered pages. So "the
manual" is still an exception rather than an exception list, and the rule has
not had to say what makes a page readable.

What this does not read is the corpus for a session reporting a thin answer.
That is the first **Wrong if**'s own stated route — "the first report of that
would be a session's" — and it stays the thing to watch.
