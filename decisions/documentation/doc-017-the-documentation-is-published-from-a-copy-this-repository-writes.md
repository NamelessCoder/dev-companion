---
id: D-DOC-017
date: 2026-08-06
status: open
---

# D-DOC-017 — The documentation is published from a copy this repository writes

**`documentation/` is published as a site on GitHub Pages, generated from the
copy `bin/cli documentation:build` writes rather than from the sources.**

That directory was written for a reader holding the whole checkout, and a third
of what it points at is a directory the site does not carry.

## Evidence

- 87 of the 246 relative links below `documentation/` leave it: 39 into
  `decisions/`, 19 into `requirements/`, 9 at `AGENTS.md`, 6 into `scenarios/`,
  5 into `todo/`, 5 into `src/`, 2 into `skills/` and 2 at the root readme.
  Served as they stand, every one of them is a 404.
- Eleven of its directories carry their own page as `readme.md`, and a generator
  publishes a directory as `index.md`.
- Nothing else in the corpus needs handling. No page carries front matter, none
  of the 582 fenced blocks holds a relative link, and the whole tree is 1.1 MB.
- GitHub Pages serves the repository root or `/docs` when it deploys from a
  branch, and this directory is neither.
- A Python renderer would be the only one in a repository whose whole upkeep is
  `composer` and `bin/cli`. Material for MkDocs and Zensical each build this
  corpus, and each brings a second toolchain into CI to do it.
- phpDocumentor Guides renders it in PHP: 47 pages, with the tables, the code
  blocks, the images and the page links. It is what docs.typo3.org is rendered
  with, so the corpus and its ecosystem agree.
- It costs two things, both measured. There is no search of any kind, and a link
  naming a heading in another page is discarded whole — 33 of them, all into
  `answer-sources.md`, the text left standing where the link had been.
- It cannot go in this package's `require-dev`. Resolving it there drags
  `symfony/string` to 8.1, which needs PHP 8.4.1, and CI runs 8.2 and 8.3.
- No theme it ships is publishable. The default one writes a bare document — 57
  files and not one stylesheet — and `guides-theme-bootstrap` is a starter whose
  navbar is branded "Navbar" and whose menu is empty.
- A menu of its own is not on offer either. Guides builds one from a `toctree`,
  which is a reStructuredText directive that markdown has no form of, so every
  theme's navigation is empty against this corpus.
- What a template does get is the whole corpus: `env.allDocuments` holds all 47
  at render time, with a path and a title each.

## Decided

- Only `documentation/` is published. What must hold, what a change rested on
  and the order of the work stay entries in the repository, and the site links
  to them there.
- The copy carries the two changes rather than the sources, so the paths a
  reader of the checkout follows are the ones `links:check` goes on reading.
  `Site` writes it and `guides.xml` renders it, so a page can be seen the way it
  is published without anything being deployed.
- The renderer is phpDocumentor Guides, installed from `build/guides/`, which is
  a manifest of its own so that the package's own dependencies are not bent to
  fit a renderer.
- The search is written here as well, by `documentation:search`, and filtered in
  the browser. What is indexed is the prose and not the fenced blocks, which
  holds it to 213 KB and keeps a page that answers a question above the one
  whose recorded answer happens to carry the word.
- The theme is this repository's own, and one file: a layout shadowing the
  default one, with the stylesheet inlined and the navigation built from
  `env.allDocuments` rather than from a `toctree` nothing here can write.
- `t3docs/typo3-docs-theme` renders the docs.typo3.org look and was rejected. It
  carries a dependency graph of its own for a site of 47 pages, and this server
  is not TYPO3 documentation.
- A link naming a heading in another page loses the heading rather than the
  link. The copy is where that happens, so the sources go on naming the section
  a reader of the checkout jumps to.
- The navigation is the file tree. `documentation/readme.md` stays the one
  curated map, and a second one in `guides.xml` would be a list of 58 entries
  that drifts from it silently.
- Where the sources are is `composer.json`'s `support.source`, which already
  declares the package to everybody else.
- Every push to `main` deploys. A filter naming the paths that can change the
  site is a second statement of what the site is made of.

## Assumed

- That a reader follows a link into the repository rather than expecting the
  entry on the site. Nothing measures which of the two they take.
- That what a reader looks for is in the prose. A term that this corpus only
  ever writes inside a fenced block is not indexed, and `runTests.sh` was one
  keystroke away from being such a term.
- That the way out stays cheap. `Site` writes plain markdown and a renderer only
  consumes it, so a second renderer costs `guides.xml` and the flags around it —
  which is what the two Python ones were measured at before this one was.

## Wrong if

- A link on the site goes nowhere. A link that left points at a path on GitHub
  that nothing here re-reads, so a decision that is renamed breaks it silently.
  `fail-on-error` does not catch it either: the renderer reports an unresolved
  reference as a warning, and one warning stands that will not be removed.
- A search returns the wrong page first, because nothing here ranks beyond the
  title, the headings and how often a word occurs in the prose.
- The repository moves and `support.source` still names where it was.
- A page is added and lands in the sidebar where nobody looks, because the order
  there is alphabetical and the reasoned one is on the map page alone.
- A recorded tool answer reads differently on the site than in the checkout,
  because a fenced block acquired a relative link and the rewrite treated it as
  one — which is what `Links` does everywhere else.

## Covered by

- `SiteTest::noPublishedPageKeepsALinkToAFileTheSiteDoesNotCarry`
- `SiteTest::everyLinkThePublishedCopyKeepsResolvesInsideIt`
- `SiteTest::aDirectorysOwnPageIsPublishedAsItsIndex`
- `SiteTest::noPublishedLinkNamesAHeadingInAnotherPage`

## Since then

The stylesheet is no longer inlined, and the theme no longer writes everything
itself. What was weighed here at 4 KB reached 16 KB on every page, and
[`D-DOC-019`](doc-019-the-sites-stylesheet-and-script-are-built-files-and-what-is-solved-is-taken-from-a-package.md)
is what was decided on the measurement — including the cache buster this entry
counted as a moving part, which Twig's `source()` makes two lines.

Two of the bullets under **Decided** no longer describe the site. `readme.md` is
published as well, as the page it opens on, and the map of `documentation/` is
served below it — a visitor arriving is deciding whether this server is for
them, and the map answers a different question.
[`D-DOC-018`](doc-018-the-site-opens-on-the-readme-and-the-map-is-a-page-below-it.md)
is what settled that, and what remains true here is everything about how the
copy is written and rendered.

The three commands this entry names are one. `documentation:build`,
`documentation:assets` and `documentation:search` were a sequence a person had
to keep in the right order and could not, and
[`D-DOC-020`](doc-020-the-site-is-rendered-by-one-command-that-installs-what-it-needs.md)
folded them into `bin/cli documentation:render`. What is published, and that it
is a copy, is unchanged.

The two cases this entry named over the search index went with the index. It is
the theme's now, written from the rendered pages rather than from the copy —
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-and-this-repository-keeps-none-of-it.md)
— so what a hit is named by is no longer this repository's to hold. The four
cases over the copy itself are untouched, which is what this entry is about.
