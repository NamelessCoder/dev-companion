---
id: D-DOC-020
title: The site is rendered by one command that installs what it needs
date: 2026-08-09
status: revoked
revokedBy: D-DOC-028
---

# D-DOC-020 — The site is rendered by one command that installs what it needs

**`bin/cli documentation:render` is the whole of a local render: it installs
what is missing, builds the assets, writes the copy, renders it, and publishes
what goes beside the pages.**

The six steps were written down in the order they have to run and nobody could
keep them, which is a sequence rather than a set of choices.

## Evidence

- The published recipe was six commands and left out the one that installs the
  renderer. Following it in a fresh checkout ran
  `build/guides/vendor/bin/guides` against a path that does not exist, because
  `/build/guides/vendor` is gitignored.
- Not one of the six is optional and not one of the orders is free. The assets
  carry a hash the layout reads with Twig's `source()` while it renders, and the
  last two write into a directory the renderer creates and sweeps.
- The three commands were three classes over 130 lines that between them called
  four `Site` methods, and each printed one line. Two of them existed because
  the renderer sits between them.
- `.github/workflows/documentation.yml` had the same sequence a second time, as
  a shell block. It is now the one command, with the installs kept as their own
  step so the `node_modules` cache the workflow declares is used.

## Decided

- One command, and the three it replaces are deleted. `documentation:build`,
  `documentation:assets` and `documentation:search` are gone as names.
- It installs what is missing rather than reporting it. `checkouts:update` and
  `environment:create` already make what they need, and both installs write into
  gitignored directories below `build/guides/`.
- Every step that leaves this process is printed as the command a person could
  have typed, and a failure quotes it with its output. The first render on a
  cold machine is minutes of `composer install`, and a build that dies has to
  say which step.
- It ends by printing `php -S localhost:8000 -t .site/html`, because the search
  fetches its index beside the pages and a browser refuses that over `file://`.
  A site opened from disk looks whole and has no search.
- `Site` keeps the four steps that are this process and gains the two that are
  not, as `BUILD_ASSETS` and `RENDER`, so what the site is made of stays in one
  file.
- The directory it builds into is an argument, defaulting to what `guides.xml`
  names. That is what lets the suite drive the whole command without writing
  into this checkout — the same argument `documentation:build` carried, for the
  same reason.

## Assumed

- That `composer` and `npm` are on the PATH of somebody rendering the site. Both
  were already required by the recipe this replaces.
- That an install is wanted where something is missing. Somebody who wants only
  the render on an unprepared checkout has no way to say so.
- That the renderer's own output is not worth printing on success. It writes one
  warning this repository will not remove, and `guides.xml` says why.

## Wrong if

- A render succeeds while serving a page whose stylesheet is the previous
  build's, which would mean the assets were built after the layout read their
  names.
- Somebody has to run something else before or after `documentation:render` to
  get a site they can read.
- An install runs on a machine where the checkout already had both, which would
  make every render minutes instead of seconds.

## Since then

The steps are four rather than six. The asset build is gone with the theme it
built for, and what follows the render is the theme's own finish step —
[`D-DOC-024`](doc-024-the-sites-theme-is-a-package-this-repository-keeps-none-of.md).
So the command installs what is missing, writes the copy, renders it, finishes
it and puts the dark twins beside the pages. What this entry decided is
untouched: the order is not a choice, and one command is what keeps it.

## Revoked on 2026-08-12

Its statement no longer describes this repository: there is no
`documentation:render`, and nothing here installs a renderer (`D-DOC-028`). What
it was right about survives as `documentation:preview` — the order is not a
choice, and a recipe leaving out the install fails on a missing binary. What it
lost is the second copy in the workflow, which is what this entry deleted. Three
of its six steps stopped being this repository's: the asset build went with the
theme, the dark twins were referenced by nothing, and the renderer is required
where the site is built.
