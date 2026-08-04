---
id: D-KNW-060
date: 2026-08-04
status: open
---

# D-KNW-060 — What a backend spec locates by is written where the spec is

**The markup a Playwright spec addresses the backend through belongs to the
`browser-tests` hint and to the Playwright document, not to the component
catalog and not to a skill.**

A session writing four backend specs found nothing with any of its locators,
because a backend module renders inside an iframe and nothing in this corpus
says so.

## Evidence

- `feedback/2026-08-04-175916`: four specs failed for that one reason, and what
  established the DOM was a throwaway probe spec and three extra Playwright
  runs. What it had to discover was `id="typo3-contentIframe"`,
  `name="list_frame"`, the tile `.t3-page-ce` carrying
  `id="element-tt_content-<uid>"`, the preview body `.t3-page-ce-body`, and the
  module menu as `role=navigation` named "Module Menu" — its first guess, the
  custom element `<typo3-backend-module-menu>`, does not exist in 14.3.
- `knowledge/documents/project/testing/playwright.md` already ships a backend
  spec, and it asserts the URL alone: "whether the backend answered the module
  or the login form is a question the URL answers". So the corpus carries a
  backend spec that never enters the module, and the first spec that does is on
  its own.
- The `browser-tests` hint carries the layout — `e2e/` per module, `fixtures/`
  for page objects, `helper/` for the login setup — and no selector.
- `knowledge/catalog/components.json` is read off the core's Sass and answers
  what an author builds: a root class, its variants, its custom properties and
  markup to write. The session read that description and passed the tool over,
  which is the finding it reported as an unverified assumption. The assumption
  held.
- [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) keeps
  backend markup out of a published skill, so
  `skills/typo3-extension-testing/references/playwright.md` cannot carry it
  either.

## Decided

- The judgement is **step 1a**, the knowledge is missing. The hint that routed
  the session correctly — `content-element-preview`, which says a preview is
  asserted in a browser test — stops exactly where the selectors begin.
- It lands in two places a browser task already passes: one sentence in
  `browser-tests` that a backend module renders inside `#typo3-contentIframe`,
  and the paragraph with the tile and preview-body selectors in the Playwright
  document, beside the backend spec that is already there.
- Not the component catalog. Its entries answer what an author writes, are read
  off the core's Sass, and `bin/cli catalog:check` verifies them as markup to
  produce. What a spec locates by is the other direction.
- Written from a running backend rather than from the report. The selectors
  above are one session's reading and nothing here reproduced them.

## Assumed

- That the frame identifier is stable across the covered majors. The session saw
  it on 14.3 alone, and a selector that moved would be a statement needing
  `since` — which is what the verification step decides.

## Wrong if

- A session that has the iframe sentence still writes locators that find
  nothing. Then what is missing is a page object the suite composes from, not a
  fact.
- The tile markup turns out to differ between the covered majors and the
  statement carries no binding. Then this belonged in a catalog that is checked
  against `.checkouts/` after all.
