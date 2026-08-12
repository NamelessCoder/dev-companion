---
id: D-DOC-025
date: 2026-08-12
status: open
---

# D-DOC-025 — The documentation is four sections, and the bar carries those four

**`documentation/` is `usage/`, `server/`, `contributing/` and `records/`, and
those four are the links in the bar.**

Which section a page belongs to is decided by who reads it. Thirteen entries
stood at the top before, sorted by path, and a reader had to know the answer to
find where the answer was.

## Evidence

- The rail listed thirteen top-level entries and the bar listed none. Six of the
  thirteen were single pages that had been given a directory apiece.
- `tools/` and `resources/` stood beside the server in the tree while being what
  the server offers. Nothing else in the corpus is the thing it documents.
- The tree **is** the navigation since `D-DOC-024`: `automatic-menu` builds the
  rail and the trail out of the directories, so a grouping that was cosmetic in
  a checkout is now what a reader clicks.
- The move is 46 files, 141 links rewritten in 79 markdown files, and 13 PHP
  files that name one of those paths in a constant, a description or a comment.
  `links:check` and `LinksTest` are what made that safe to do at once.
- The theme's rail finds the section a reader is in by looking one level down.
  `server/index` and `server/tools/index` both show the fold with all 32 tool
  pages in it; the tool pages themselves fall back to listing the four sections.

## Decided

- Four sections, named for the reader rather than for the artefact: `usage/` is
  having the server answer in your own project, `server/` is what it can be
  asked and where each answer comes from, `contributing/` is working on it, and
  `records/` is what is written down and where.
- `tools/` and `resources/` sit under `server/`, because they are the server.
  The breadcrumb reads `The server / The tool surface / typo3_icon_lookup`, and
  that is the sentence the structure is meant to say.
- The bar carries the four and nothing else. It is configuration in
  `guides.xml`, not a template, and the rail below it is the section a reader is
  in — a manual with every page in the bar has said nothing.
- The map above the sections names the four and no longer lists every page. A
  section's own page lists what is in it, which is also where
  `TodoTest::everyTodoIsHandedWithThePageThatSaysHowOneIsWorked` now looks.
- The rail defect is left standing and the structure is kept. Flattening 32
  pages to suit a template is the wrong way round, and the trail on the same
  page is already right, so what the rail is missing is a reading the renderer
  hands it either way.
- The four flat index pages written earlier the same day are absorbed into the
  section pages rather than kept beside them.

## Assumed

- That a reader's first question is which of the four they are. Nothing measures
  it; what is measurable is that thirteen entries answered no question at all.
- That `usage/` and `server/` read as different things. Installing is the one
  page a user of the server needs and everything else about the server is what
  it answers with, but somebody looking for "how do I use it" may look in
  `server/`.

## Wrong if

- A page cannot be found because it is in the section the reader did not look
  in, which the split between `usage/` and `server/` is the likeliest cause of.
- A published URL that somebody wrote down is a 404. Every page moved, GitHub
  Pages serves no redirects, and nothing here rewrites an old path.
- A section grows to where its own page is a list nobody reads, which is what
  the thirteen entries were.
- The rail on a tool page keeps listing the sections, because the theme did not
  move and this was left standing on the assumption that it would.

## Covered by

- `SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`
- `LinksTest::everyPathThisRepositoryWritesToItselfResolves`
- `TodoTest::everyTodoIsHandedWithThePageThatSaysHowOneIsWorked`
- `TodoTest::everyClaimIsHandedWithThePageThatSaysHowSeveralAreWorked`
