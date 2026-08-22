---
id: D-CAT-002
title: 'The index of worked examples is curated'
date: 2026-07-29
status: revoked
---

# D-CAT-002 — The index of worked examples is curated

**The path of a worked example is machine-checked against every covered
checkout; the sentence about what it is a reference for is not.**

An index of directories inside the core is the kind of answer that rots
silently: a moved path leaves a caller reading a miss as "I looked in the wrong
place". `bin/cli catalog:check` therefore re-derives each entry's range from
whether the path is there in every covered checkout, the way the component and
system-extension catalogs are derived.

## Decided

- The path is machine-checked, the sentence about what it is a reference for is
  not. Existence is what a script can know; whether `theme_camino` still
  demonstrates the layout is a judgement, and a wrong judgement here costs one
  read, while a wrong path costs the caller their trust in the tool.

## Assumed

- The list stays short enough to reread by hand when a major lands — seven
  entries, each a subject a session actually needed. The value is one line per
  directory, not coverage.

## Wrong if

- An entry's path still exists while what is inside moved —
  `Build/tests/playwright/e2e` surviving a rewrite that puts the fixtures
  somewhere else. Then the check has to descend into the entry, and the honest
  form is naming the two or three files that carry the shape rather than the
  directory.

## Revoked on 2026-08-01

On the entry this named, and it had already happened. 13.4 has
`Build/tests/playwright/e2e` with two spec files loose in it and the
accessibility scan a Playwright project of its own beside it; the layout the
entry describes — one directory per module, the scan among them — arrived in
14.3. Existence passed the entry on v13 and reported nothing. The entry now
names the four files that carry its sentence:
`e2e/accessibility/modules.spec.ts`, `fixtures/backend-page.ts`,
`helper/login.setup.ts` and `Build/playwright.config.ts`. `catalog:check` reads
those as well as the path, the derived range moved to v14, and a v13 caller is
left without the entry rather than sent to a layout their checkout does not
have. Only this entry names files; for the other six, existence is still all
that is checked.
