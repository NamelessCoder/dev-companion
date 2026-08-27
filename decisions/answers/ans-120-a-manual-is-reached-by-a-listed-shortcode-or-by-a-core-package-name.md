---
id: D-ANS-120
title: A manual is reached by a listed shortcode or by a core package name
date: 2026-08-27
status: open
coveredBy:
  - PermalinkTest::aManualThatRenamedItsShortcodeIsReported
  - PermalinkTest::aShortcodeReachesTheManualItNames
  - PermalinkTest::anUnknownShortcodeSaysWhichOnesAreKnown
---

# D-ANS-120 — A manual is reached by a listed shortcode or by a core package name

**The manuals `typo3_permalink_lookup` answers for are the named ones in
`knowledge/manuals.json` plus every manual of a package the core ships, whose
shortcode is that package's own name.**

This is the question `D-ANS-118` left to the todo. The named half is a
maintained list because nothing on the host publishes the set; the package half
needs no list at all.

## Evidence

- A manual declares its own shortcode and republishes it. It is the
  `interlink-shortcode` of the `<extension>` element in that manual's
  `guides.xml` — readable in `.checkouts/main` for all 22 system extensions that
  ship one — and the theme writes it into every rendered page as
  `data-interlink-shortcode`. So the claim is checkable one entry at a time, and
  `bin/cli manuals:check` is that reading.
- Nothing publishes the set. `docs.typo3.org/permalink/` and
  `/permalink/<shortcode>` are 404, there is no `sitemap.xml`, `guides.xml` is
  not part of the rendered output, and the homepage links nine manuals — not
  `guide-contributionworkflow`, whose shortcode `t3contribute` resolves. Read on
  2026-08-27.
- The shortcode is not derivable from the manual's path. `t3tsref` is
  `reference-typoscript`, `t3contribute` is `guide-contributionworkflow`,
  `h2document` is `docs-how-to-document` and `changelog` is `typo3/cms-core`.
  Twelve manuals were read for their own declaration and all twelve agree with
  the list.
- A package's manual is the exception and derives. `typo3-cms-felogin:start` and
  `typo3/cms-felogin:start` both answer 307 with
  `/c/typo3/cms-felogin/main/en-us/Index.html#start`, and the manual declares
  `typo3/cms-felogin` — the Composer package name, with the collection letter
  the only thing the path adds. The same holds for `typo3-theme-camino` and for
  `typo3-cms-core`, which reaches the manual `changelog` also names.
- An extension outside the core is published on another axis.
  `georgringer-news:start` answers 307 with `/p/georgringer/news/main/en-us/`,
  and `/p/georgringer/news/14.3/` is 404 — that manual is versioned on the
  extension's releases, so a TYPO3 version selects the wrong branch of it or
  none.
- A shortcode a core package declares is not a manual that exists.
  `typo3-cms-styleguide:start` is 404 although `typo3/sysext/styleguide` ships a
  `guides.xml` declaring `typo3/cms-styleguide`.
- A version the host does not publish is served as `main` with no 303 the caller
  sees. `/c/typo3/cms-core/13.4/en-us/objects.inv` and
  `/m/typo3/guide-contributionworkflow/13.4/en-us/objects.inv` both redirect to
  `main`, and the inventory's own `# Version:` line is where that is visible.

## Decided

- `knowledge/manuals.json` is the one list of manuals this server knows, and
  `Manual\Manuals` is what reads it. The constant `Documentation` carried was a
  second list of the same manuals in PHP and is gone: the entries it held carry
  `searched: true`, and which four those are is unchanged.
- Twelve manuals are named: the four searched, the core changelog, the three
  tutorials, the contribution guide, the documentation guide, the render-guides
  manual and Fluid's own.
- A shortcode matching `typo3/cms-*`, `typo3-cms-*`, `typo3/theme-*` or
  `typo3-theme-*` is a package manual under `c` and needs no entry. Both
  spellings answer, and the answer names the manual's own.
- An extension manual under `p` is not answered for, which is `D-ANS-118`'s
  assumption with a reason: its branches are the extension's releases and the
  argument this tool takes is a TYPO3 version.
- The branch that answered is read off the inventory and reported. The status
  code cannot say it, and `R-DOC-001` forbids passing a `main` answer on as an
  answer for an LTS.
- `bin/cli manuals:check` reads each manual's own page for what it declares. It
  is a command rather than a test because it reaches the host, and it is the
  only one here that does.

## Assumed

- That every manual publishes its inventory at `<base>objects.inv` in version 2
  of the format. It was read that way for all twelve named manuals and for three
  package manuals.
- That the collection letter is stable per manual. It is part of the URL and
  nothing states it as a contract, so a manual moving between `m` and `other` is
  a 404 until the list is corrected.
- That a package manual is always at `/c/<package>/`. Every core package manual
  read is, and no rule on the host says so.

## Wrong if

- A named manual renames its shortcode or moves collection. Every identifier of
  it stops resolving, and `bin/cli manuals:check` is what reports it — nothing
  in the suite can.
- A session asks for a manual outside the twelve and the packages, and is told
  the shortcode is unknown for something that resolves on the host. Then the
  list is short and the entry that is missing is the evidence for adding it.
- An extension manual becomes the ordinary question. Then the boundary is in the
  wrong place and what the tool needs is the extension's own version rather than
  TYPO3's.
- The permalink route stops accepting the dashed spelling of a package name.
  Then half the identifiers the core itself writes are reported as resolving and
  answer 404.
