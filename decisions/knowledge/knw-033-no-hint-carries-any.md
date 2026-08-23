---
id: D-KNW-033
title: 'No hint carries `any`'
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::aFrontendThemeIsNotAnsweredWithTheBackendsOwnCssConventions
  - HintsTest::hintsAreGroupedByDomainWithPhpFirst
  - HintsTest::nothingIsTaggedAnyWithoutSayingWhy
  - ScopeTest::aCoreContributorOnFrontendLosesTheBackendUiSections
---

# D-KNW-033 — No hint carries `any`

**The 38 hints that carried `any` name the domains they are really asked from,
and the withholding rule reads a hint's domains rather than the query's.**

`any` was `general.json` renamed in `D-KNW-029`, and `D-KNW-032` showed that
splitting does not dissolve it: the share stood at 63% over 38 of 120 hints
because a split inherits the tag it was split from.

## Evidence

- Three of 41 scenario answers moved, and the whole corpus of hint titles and
  prompts was dumped before and after to say so. `CORE-03` — a commit message
  came back from review — loses `frontend-records`, which the always-selected
  bucket had been handing it. `SKILL-02` gains `sitepackage-initial-content`,
  `SKILL-05` gains `core-tests`.
- Nothing lost its answer: the same ten prompts reach nothing as before, every
  hint is still reached by its own title, and the `any` share is 0 of 120.
- Two domains were detected and had nowhere to go, which is half of why
  `general.json` held what it did: `xliff` and `docs`. Labels, the translation
  domain, label retirement and the changelog are theirs.
- Four vocabulary gaps showed up as answers that got worse, each fixed where it
  belonged: `icon` and `upgrade` were not PHP words, `translate` was not an
  XLIFF word beside `translation`, and `backend layout` was neither Fluid nor
  TypoScript. Each had been carried by `any` rather than by the vocabulary.

## Decided

- A domain tag is what a query selects by, so a hint carries every domain a
  caller could arrive from and no more. `content-elements` is PHP, Fluid and
  TypoScript; `preview-record-variable` is Fluid alone.
- The first tag is the heading, so it is the domain the hint is most about.
  `extension-asset-build` leads with PHP rather than CSS: it is a build setup,
  not a backend design-system rule, and the heading is what a reader believes.
- The frontend withholding drops a hint whose domains are `css` and `typescript`
  and nothing else, rather than removing those two domains from the selection.
  Those two tags mean the backend's own design system; a hint that carries a
  third domain is not that hint. Building a sitepackage's assets and scanning a
  site for contrast are asked in exactly the same words and used to be withheld
  with it.
- `any` stays a tag nobody uses. `HintsTest::nothingIsTaggedAnyWithoutSayingWhy`
  fails on the first new one, because a hint every query selects is a decision
  rather than a tag picked while writing.

## Assumed

- The tags are right where nothing measured them. Three answers moved, so 37 of
  the 38 were only ever reachable through the bucket, and what they are really
  asked from is a judgment per hint that the scenario prompts do not all
  exercise.
- Losing `frontend-records` from a commit-message review is an improvement
  rather than a regression. Nothing in that prompt is about rendering records.

## Wrong if

- A question comes back with nothing where it used to be answered, and the hint
  that would have answered it is one domain away. That is a tag too narrow, and
  the fix is the tag rather than a new `any`.
- A hint is tagged with four or five domains to be found, which is `any` spelled
  longer and the failure this replaced.
- The withholding lets a backend Sass convention through to a theme extension,
  which would mean the domains-subset test is the wrong rule for it.
