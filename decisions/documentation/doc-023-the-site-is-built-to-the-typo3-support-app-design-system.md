---
id: D-DOC-023
title: The site is built to the TYPO3 Support App design system
date: 2026-08-09
status: revoked
revokedBy: D-DOC-024
coveredBy: []
---

# D-DOC-023 — The site is built to the TYPO3 Support App design system

**The site is built to the TYPO3 Support App design system: its tokens are
vendored below `theme/assets/tokens/`, its icons below `icons/`, and `site.css`
only arranges them.**

What the site looked like had been decided three times in one afternoon and each
time by taste, because nothing here was the source for it. There is one, and it
is the product's own.

## Evidence

- The palette was rewritten three times in one session — warm neutrals, then
  matte with a mint accent, then matte with a burnt orange — with contrast
  measured each time and the choice itself resting on nobody.
- The design system is a design-system project on claude.ai/design, read with
  `DesignSync`. It carries six token files, 33 icons from `TYPO3/TYPO3.Icons`
  under the core's own identifiers, two vendored families, a component layer,
  and build rules whose first line describes this server.
- Four of its rules were being broken: the search dialog carried a shadow, the
  magnifier was drawn here rather than taken from the set, the accent was a
  colour this repository picked, and the two families were not used at all.
- The system's own answer to light and dark is what this site already had — one
  set of `light-dark()` pairs against `color-scheme` — so the mechanism
  `D-DOC-022` settled survived the change and only the switch above it moved.
- The faces publish 245 KB across twelve files, of which a reader of this corpus
  fetches about 84 KB: the latin-ext half sits behind a `unicode-range` no page
  here has a character for.
- The stylesheet grew from 11.6 KB to 17.8 KB, and both are one request for the
  whole site — `D-DOC-019`.

## Decided

- The token files are copied unchanged and are not edited here. A value that
  differs from the system's is the one thing that directory exists to prevent,
  and `theThemeWritesNoColourOfItsOwn` holds `site.css` to naming them. That
  check went with the file it read; the **Revoked on** section below says what
  stands in its place.
- The icons are copied, not drawn. The system contributes a missing icon
  upstream rather than inventing one locally, and the identifiers are the core's
  own — the same strings `typo3_icon_lookup` returns.
- Every icon is inlined with Twig's `source()`, because an `<img>` cannot
  inherit `currentColor`. The favicon is the one exception, since a browser tab
  is not a place a page's colour reaches.
- The faces are built out of the two `@fontsource` packages by `build.mjs`,
  latin and latin-ext, woff2 alone. The system vendors them rather than pulling
  from a font host so a page sets in the right type behind a strict content
  policy or with no network, and that reason holds here.
- The faces are `font-display: optional` and the two the header sets are
  preloaded. `swap` re-laid the wordmark out when the face landed, which is a
  jump on every navigation because each document runs its own font loading
  whatever the cache holds. Optional forbids the swap after the paint, so the
  cost moves to one page: a reader arriving with a cold cache reads it in the
  fallback, and every page after it is set.
- A drawing is not prose. The renderer wraps a standing image in a paragraph,
  and the paragraph carries the 66ch measure, so a 1200px drawing arrived at
  556px — 0.46 of the size it was drawn at, and its 13px floor at 6px on the
  screen. It takes the column now, 0.67, and the lightbox is where it is read at
  size.
- The accent marks the page being read in the rail and the pipe in the wordmark,
  and nothing else. The system's third place — the shell prompt — has no markup
  here, because a fenced block is one string the renderer hands over.
- The chosen hit in the search is the quiet accent surface rather than the
  accent, which is the system's treatment for a selected row and keeps the
  filled accent meaning one thing on the page.
- highlight.js is mapped onto the three syntax colours the system declares. A
  fourth would be a colour nobody declared.
- The mode switch is the system's two segments, so the three-state button
  `D-DOC-022` decided is gone.
- The drawings are the system's three where it has one, and the other eight are
  brought onto its palette: the category colours they carried — a blue stroke, a
  green one — become hairlines, because the system's diagram vocabulary is
  neutral nodes, one orange, and status colour only where the drawing is about
  status.
- Every drawing ships twice and the dark file is a straight token swap of the
  light one. Which is shown is the script's decision rather than a `<picture>`
  query, because a `media` query reads the machine and this page can be held in
  the other mode against it — and Site::publishDrawings() puts the twin nobody
  named beside the one a page did.
- A drawing is inlined into the page rather than linked. An `<img>` is a
  document of its own and cannot see this page's `@font-face` rules, so the type
  inside every drawing was the reader's own fallback — measured on a machine
  with neither family installed, the same file rendered 169px wider as an
  `<img>` than inlined. Laying columns out against a face nobody has is what
  made the spacing look wrong however often it was corrected. The markup keeps
  the `<img>` until the script runs, so a browser without one still gets the
  drawing.
- A drawing is drawn at the width it is shown at. The system's floor is 13px *at
  drawn size*, and 1200px of drawing in an 804px column is 0.67 of it — which no
  coordinate inside the file can repair. The redrawn ones are 800 wide; the rest
  are not redrawn yet and are listed as such.

Two things depart from a specimen, which the system asks to be named rather than
forbidden:

- The rail is 210px and the tool names do not fit it:
  `typo3_system_extension_lookup` is 29 characters of mono at 13px. They wrap
  rather than truncate, because a truncated identifier is not the identifier.
- The wordmark reads `TYPO3 | Dev Companion`. The system's own says
  `Support App`, which is this product under the name the design system was
  written for.

## Assumed

- That the design system is this product's. Its build rules describe a local MCP
  server in plain PHP that helps coding agents implement, review and verify
  TYPO3 work, which is what this repository is.
- That a reader of this corpus needs latin alone, and that publishing latin-ext
  beside it costs nothing because the browser fetches by `unicode-range`.
- That the system moves under this checkout without telling it. Nothing re-reads
  it; a token that changed there is a copy that has to be made again.

## Wrong if

- A token below `tokens/` differs from the system's, which is what makes the
  vendored copy worse than no copy.
- A reader fetches a face for a subset no page here uses, which would mean the
  `unicode-range` did not survive the build.
- A whole site reads in the fallback, because `optional` needs the face in the
  cache and the host serves it without a cache header. `php -S` is one such
  host, so the local preview shows exactly that and says nothing about what is
  deployed.
- The accent appears somewhere that is not the page being read or the pipe.
- A drawing loses what its colours were carrying. Eight of them grouped by hue
  and now group by position and label alone, which is the system's own
  vocabulary and is also the change most likely to have taken meaning out.
- The mark or the signet is read as TYPO3 endorsing this package. The system
  keeps the Soul out for exactly that reason, and nothing here may put it back.

## Since then

The first **Wrong if** happened. `c2bef123` found `--text-muted` at `#8A8378`
and `--status-warn` at `#A56A00` in the vendored copy, months after the system
had raised both to reach WCAG AA — so the published documentation was below it
in both modes for as long as the copy stood. The third **Assumed** is what it
cost: nothing re-read the system, and a copy nobody re-pulls is wrong without
saying so.

`site.css` imports the tokens and the component layer from
`@typo3/soul-design-system`, pinned to a commit, and `theme/assets/tokens/` is
gone. **The statement's token clause therefore no longer describes this site**,
and whether this entry is revoked with a successor written for what holds
instead is a question for the maintainer rather than something to decide in
passing: the icons under `icons/` are still copies with the same defect in
smaller form, `c2bef123` says they come next, and an entry rewritten before that
move would be rewritten twice.

On 2026-08-11 the two assertions above were put back on the theme that exists.
The one that held every token the theme names to what the system declares read
the vendored directory and had been throwing since it went; that check runs in
`build.mjs` over the bundled stylesheet now, where the imports are resolved —
the tokens arrive with `npm ci`, which `composer ci` does not run, and no test
here may skip itself (`StructureTest::noTestSkipsItselfInsteadOfHolding`). It
reads wider than the assertion did, since the component layer is in scope, and
it ignores a `var()` carrying a fallback: `--sds-pad` is declared nowhere and
the padding beside it is the value. `theThemeWritesNoColourOfItsOwn` now strips
comments before the scan, because `51e70499` explained this rule in the header
of `site.css` by naming the two values it was broken with, and the scan read the
explanation as the breach.

## Revoked on 2026-08-12

By its own third **Assumed**, and then by the move it made necessary. Nothing
below `theme/` is left to vendor: the design system publishes itself as a
renderer theme, and this site installs it —
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md).
Neither `theme/assets/tokens/` nor `icons/` nor `site.css` exists, so no part of
the statement describes this site.

The two tests it named are gone with the files they read. What holds in their
place is the package: a token cannot be redeclared here because there is no
stylesheet here, and an icon cannot be a stale copy because the sprite ships
with the theme.

Three of its readings outlive it and are what
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md)
carries forward as open: a drawing has to be read at the size it was drawn at,
which is what the lightbox was for; a truncated identifier is not the
identifier, and the rail truncates one again; and a drawing that cannot be told
which mode the page is in reads in the wrong ink, which is what the dark twin
was drawn for and what nothing swaps today.
