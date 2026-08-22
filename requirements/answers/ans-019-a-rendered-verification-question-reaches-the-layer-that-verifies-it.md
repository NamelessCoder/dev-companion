---
id: R-ANS-019
title: 'A rendered-verification question reaches the layer that verifies it'
status: held
restsOn: [D-KNW-017]
---

# R-ANS-019 — A rendered-verification question reaches the layer that verifies it

**A caller asking whether something renders correctly is told which layer
establishes that, without having named Playwright, a browser or an end-to-end
test in its own words.**

The knowledge is not what is missing. `browser-tests` and its two neighbours say
what a browser test is for, that a functional test through
`executeFrontendSubRequest()` is a rendering test rather than a frontend one,
and what the core's own suite does — and every route to them opens on vocabulary
the caller has to supply first.

The caller who needs the layer is the one who has not yet decided that a browser
is involved. A session verifying an element by reading the HTML it curled is not
asking for Playwright; it is asking whether the page came out right, and that is
the point at which the layer is still cheap to choose.

So the crossing is owed by the answers such a question does reach, and by the
prompts this repository measures itself with: a scenario asking for the outcome
— a smoke test before a deployment, browser coverage after a regression — has to
reach the cell as well.

## From

`feedback/2026-08-01-003533` (2026-08-01), a TYPO3 14 testimonials session in
`/home/benji/projects/site-new` that verified the rendered frontend and the
backend page-module preview by curling HTML and reading vendor source, in a
project that already had a Playwright harness.

Measured on 2026-08-03: all four rendered-verification phrasings `D-KNW-017`
lists reached `content-elements` or `content-element-preview` and nothing that
named a test layer, and `bin/cli hints:coverage` reported `browser-tests` among
the hints no scenario prompt reaches — `SKILL-06`, the scenario written for it,
reached no hint at all.

## Held by

- `HintsTest::aRenderedVerificationQuestionReachesTheLayerThatVerifiesIt`
- `HintsTest::theBrowserLayerIsReachedByAPromptThatNamesOnlyTheOutcome`
