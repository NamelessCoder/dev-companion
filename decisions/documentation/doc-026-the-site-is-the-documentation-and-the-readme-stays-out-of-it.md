---
id: D-DOC-026
date: 2026-08-12
status: open
---

# D-DOC-026 — The site is the documentation, and the readme stays out of it

**The published site is `documentation/` and nothing besides, so its front page
is `documentation/readme.rst`, and the repository's `readme.md` is the landing
page of the checkout alone.**

What the site owed a visitor arriving at it was in one file that also had to
work as a GitHub landing page, and
[`D-DOC-018`](doc-018-the-site-opens-on-the-readme-and-the-map-is-a-page-below-it.md)
published that file rather than writing the manual it was missing.

## Evidence

- `D-DOC-018` rejected the page below `documentation/` on one ground: it would
  put the promise paragraphs in the checkout twice. That holds for a copy and
  not for a move, and this is a move — what the readme said about the three
  sources, the trust boundary and the conventions is now on the front page of
  the manual and nowhere else.
- Most of what the readme carried was already written a second time below
  `documentation/`. The resource list is `server/resources/readme.md`, the tool
  list grouped by source is the generated `server/tools/answer-sources.md`, and
  the feedback workflow is `records/readme.md`. Those copies did drift: the
  grouped tool list went stale by five tools, and the test added to watch it is
  what `D-SCO-011` and `D-KNW-035` named until this change.
- The config a renderer reads is `guides.xml`, and its own convention in TYPO3
  is that it sits beside the corpus as `Documentation/guides.xml`. That was
  unavailable while the corpus was a directory plus one file above it.
- One source, one root. `Site::sources()` began with a constant and then read a
  directory, `Site::published()` had two special cases before its rule, and the
  map of `documentation/` was served under a third name nothing else in the
  checkout used.

## Decided

- `Site::FRONT` and `Site::MAP_PAGE` are gone. Every page the site serves is a
  file below `documentation/`, and `documentation/readme.rst` is published as
  `index.md` by the rule every other directory's page already followed.
- The front page carries what the readme's opening paragraphs carried, and the
  four sections below it. It is the page `AGENTS.md` now names as the promise —
  the first thing that becomes false when a capability changes.
- `readme.md` at the root keeps the title, the experimental note, the covered
  lines, the quickstart and the way into the manual. That is what somebody
  arriving at the repository needs before they decide to read further, and it is
  the whole of the deliberate overlap.
- A link from the manual to the readme leaves the tree and is rewritten to the
  file on GitHub, like any other link out of it. Nothing about `Site::page()`
  changes for it.
- The covered lines are named in both places, because both are somewhere
  somebody arrives, and
  `VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine` holds both to
  `knowledge/versions.json`.
- The grouped tool list is not moved. `answer-sources.md` is the same statement
  generated from the `Source` enum, so the test that watched the hand-written
  one is deleted rather than retargeted, and `ToolSurfaceTest` is what holds the
  surface to the registry now.

## Assumed

- That nobody has a deep link into the site. `index.html` is another page than
  it was and `how-the-work-is-done.html` is gone; this is a 0.x package whose
  surface has moved before, and `D-DOC-018` assumed the same thing eleven days
  earlier when it moved these two.
- That the readme stays short. It is the one file where the promise can be
  restated by somebody who does not know the front page carries it, and nothing
  measures the overlap.

## Wrong if

- The readme grows back. Two statements of what the server is, going false
  separately, is what `D-DOC-018` was right about; only the direction it fixed
  it in has changed.
- Somebody arriving on GitHub cannot tell what the server does. The landing page
  is now four paragraphs and a link, and what it leaves out is everything the
  site opens with.
- The front page reads as the map it replaced. It has to answer "is this for me"
  above "how is the work done here", and the four sections come last for that
  reason.

## Covered by

- `SiteTest::theSiteOpensOnTheDocumentationsOwnPage`
- `SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex`
- `SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex`
- `VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine`
