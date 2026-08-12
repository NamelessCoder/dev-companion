---
id: D-DOC-018
date: 2026-08-09
status: revoked
revokedBy: D-DOC-026
---

# D-DOC-018 — The site opens on the readme and the map is a page below it

**The published site's front page is the repository's own `readme.md`, and
`documentation/readme.md` is served below it as `how-the-work-is-done.md`.**

Somebody arriving at the site is deciding whether this server is for them, and
what they were shown was the map of how this repository works on itself.

## Evidence

- The site published `documentation/` alone, so its front page was
  `documentation/readme.md`, whose first sentence is "One page per procedure
  that is long enough to get wrong from memory". Nothing on it says what the
  server is, what it answers or how it is installed.
- All of that is written, in `readme.md` at the root: what the server does, the
  three sources it answers from, the trust boundary, the quickstart, the tool
  surface and the resources. The site carried none of it.
- Writing a second entry page below `documentation/` would put the promise
  paragraphs in the checkout twice. Those are what goes false first when a
  capability changes, and two copies of them go false separately.
- The readme's own links are eight, and every one of them is either external, a
  page the site serves, or `AGENTS.md`, which the copy already rewrites to
  GitHub.
- The readme named three of its links by their path —
  `documentation/server/tools/` as the text of a link to
  `documentation/server/tools/readme.md`. On the site those paths do not exist,
  because `documentation/` is served at the root.
- Which TYPO3 lines the server answers for was in no sentence of the readme, and
  it is the first thing a user checks. It is declared in
  `knowledge/versions.json`, so a test can hold the sentence to it.

## Decided

- `Site::FRONT` is `readme.md`, published as `index.md`. The map keeps its place
  in the tree and is published under its own title, so no directory here has to
  stop calling its own page `readme.md`.
- A link is resolved against the repository and named again from the published
  path, rather than the last segment being swapped in place. The front page sits
  a directory above the tree the rest of the site is, so the old rule — only the
  last segment moves — held for no link it writes.
- The three links the readme named by path are named by what they are instead.
  That is the better sentence in the checkout too, which is why it is a rewrite
  and not a rule about the copy.
- The covered lines are named in the quickstart, and
  `VersionsTest::whatSomebodyArrivesAtNamesEveryCoveredLine` holds them to
  `knowledge/versions.json`.
- The sidebar runs front page, then the pages belonging to no subject, then each
  subject. Sorted by path alone a loose page lands between two directories and
  reads as part of whichever one it fell into.

## Assumed

- That a visitor of the site is a user first. Nothing measures who arrives
  there; what is known is that the readme is written for them and the map is
  not.
- That nobody has a deep link into the site. `documentation/readme.md` was
  served as `index.html` and is now `how-the-work-is-done.html`, and this is a
  0.x package whose surface has moved before.

## Wrong if

- A reader of the checkout stops finding the map, because it is now named in
  prose as a published page rather than as `documentation/readme.md`.
- The front page grows the sections a site wants and the readme stops being
  readable as a GitHub landing page — one file serving two places is what makes
  that possible and what would make it a compromise.
- A covered line is added and the readme names it without the server answering
  for it, because the test holds the sentence to the declaration in one
  direction only.

## Covered by

- `SiteTest::aPageBelowTheFrontOneReachesItAsTheIndex`

## Revoked on 2026-08-12

By its own second **Wrong if**, read the other way round. It saw that one file
serving two places would become a compromise, and expected the readme to grow
the sections a site wants. What happened instead is that the site kept a landing
page written for GitHub, and the manual had no page saying what the server is.

The ground it stood on was that a second entry page would duplicate the promise
paragraphs. It would have — as a copy. Moving them costs nothing twice, and
[`D-DOC-026`](doc-026-the-site-is-the-documentation-and-the-readme-stays-out-of-it.md)
is that move: the front page is `documentation/readme.md` again, carrying what
this entry was right that it lacked.

What outlives it is everything it found missing on the map, and the reading that
put it there: a visitor of the site is a user before they are a contributor, so
the four sections come last on the front page rather than first. Its rule for
resolving a link — against the repository and named again from the published
path — outlives it too, and is what makes a link to the readme a file on GitHub
now.
