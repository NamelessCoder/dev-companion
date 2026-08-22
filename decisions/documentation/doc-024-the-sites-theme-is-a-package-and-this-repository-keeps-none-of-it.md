---
id: D-DOC-024
title: "The site's theme is a package this repository keeps none of"
date: 2026-08-12
status: open
coveredBy:
  - SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage
---

# D-DOC-024 — The site's theme is a package this repository keeps none of

**The site is rendered with `typo3/soul-guides-theme`, and the layout, the
assets and the search index come from that package rather than from files kept
here.**

What this repository keeps is `guides.xml`, the pages and the drawings. The
design system was published as a theme of its own, and what stood here was a
copy of it that nothing re-pulled — the defect `D-DOC-023` had already been
caught by once.

## Evidence

- The move deletes 1824 lines below `build/guides/`: a 190-line layout, a
  444-line stylesheet, a 314-line script, a 166-line asset build, two more
  templates and three manifests. What replaces all of it is one line of
  `require`.
- The npm half goes with it — esbuild, two `@fontsource` packages and the design
  system pinned to a commit — so `build/guides/` has one manifest where it had
  two, and the render no longer needs a package manager for the browser.
- `D-DOC-023`'s third **Assumed** was that the system moves under this checkout
  without telling it. It did: `c2bef123` found two vendored token values that
  had been below WCAG AA for months. A package is re-read by `composer update`;
  a copy is re-read by nobody.
- The theme is written for reStructuredText, and this corpus is Markdown. What
  that costs is the toctree: `automatic-menu` builds the same tree out of the
  directories instead, which is the mechanism the Markdown parser already marks
  its documents orphan or not for.
- Six pages sat in four directories with no `readme.md`. The renderer warns and
  attaches them to nothing, so each was in no menu at all — reachable only by a
  link inside another page.
- The theme's own finish step does the three things this repository had written
  for itself: it copies the drop-in beside the pages, draws every element ahead
  of the browser so a page reads with no script, and writes the index the search
  bar fetches.
- `sds-image` and `sds-figure` reference a drawing rather than linking it, which
  is what puts the page's own ink into it. Both work out its box from a table of
  three names inside `soul.js` — `answer-sources`, `installation-fallback` and
  `system-overview`, which are this repository's own drawings and three of
  eleven.
- A Markdown image is an inline node, and the theme renders a figure for the
  reStructuredText directive alone. So every drawing here is a plain `<img>`: no
  frame, no caption, no lightbox, and no way to be told which mode the page is
  in.

## Decided

- The theme is required as `dev-main`, because the repository carries no tag.
  What pins it is `build/guides/composer.lock`, which names the commit and is
  committed. That half no longer holds — see **Since then**.
- Everything the bar, the tab and the footer say is configured in `guides.xml`,
  under the extension element that registers the theme. Nothing here copies a
  template to change a name.
- The mark is this repository's own drawing and lives with the pages, in
  `documentation/images/`, at the three optical sizes the system draws a signet
  at. It is written in the artwork form the theme asks for — one `var()` with a
  hex fallback per shape — so a referenced mark carries the page's ink and the
  file still renders on its own.
- The search index is the theme's. What is given up with it is `D-DOC-019`'s one
  hand-written piece: fenced blocks are indexed now, and this corpus's are
  mostly recorded tool answers.
- Every directory of `documentation/` has a page of its own, held by
  `SiteTest::everyDirectoryOfTheDocumentationHasItsOwnPage`. That is the
  repository's own convention for a directory, and it is now also what makes its
  pages reachable.
- The dark twin of every drawing stays and is still published beside the light
  one. Nothing asks for it today, and it is the dark half of a drawing rather
  than a build product: deleting it would mean drawing eleven files again the
  moment they can follow the page.
- What the light files would become is a straight substitution, measured on
  `answer-sources.svg`, so it is written down here rather than measured again:
  `#1C1A17` is `--text-primary`, `#4A453D` `--text-secondary`, `#8A8378`
  `--text-muted`, `#E3DFD6` `--border-subtle`, `#C9C3B7` `--border-strong`,
  `#FBFAF7` `--surface-canvas`, `#FFFFFF` `--surface-raised`, `#986200`
  `--status-warn` and `#FF8700` `--accent` — each as a `var()` with the light
  value as its fallback. The three signets are already written that way.

## Assumed

- That `dev-main` and a lock file are a pin. The package carries no tag to ask
  for instead, so what names the commit this checkout renders with is the lock
  file and nothing else.
- That the package stays reachable where `build/guides/composer.json` names it.
- That a reader searching this site is not buried by the recorded answers. This
  is what `D-DOC-019` decided the other way with 582 fenced blocks counted.

## Wrong if

- A drawing is read at the wrong ink on a dark page, which is what it does
  today: the file is light and nothing swaps it.
- A rail item is cut off rather than wrapped. `D-DOC-023` named the tool names
  as a departure from a specimen for exactly this reason, and
  `typo3_backend_module_lookup` is truncated in the rail as it stands.
- The theme moves and this checkout renders an older one for months, which is
  the copy's defect arriving through a lock file nobody updates.
- A page is served unstyled, because the finish step did not run and nothing
  said so.

## Since then

`build/guides/` is gone, manifest and lock with it. The renderer and the theme
are required into a directory outside the checkout by whoever renders, and what
a render takes is the theme as it stands —
[`D-DOC-028`](doc-028-the-renderer-is-a-build-tool-and-this-repository-carries-none-of-it.md).
This entry's statement is untouched and is more true than it was: the theme is a
package, and this repository now keeps none of it at all. Its first **Wrong if**
became impossible with the lock: nothing here can render an older theme for
months, because nothing here records which one it rendered.

The two cases this entry named on the render command are gone with it, and the
second **Wrong if** is unguarded because of it: a page served unstyled is now
something a reader sees rather than something the suite says.

The dark drawings are gone as well. No page named one, so no rendered page ever
referenced one, and eleven files were kept against a mechanism that does not
exist. What this entry left open about them is unchanged and is now open without
the files: a reader in dark reads a light drawing.
