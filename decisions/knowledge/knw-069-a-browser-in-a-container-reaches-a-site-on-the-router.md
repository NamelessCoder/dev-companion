---
id: D-KNW-069
title: 'A browser in a container reaches a site on the router'
date: 2026-08-10
status: open
coveredBy:
  - KnowledgeTest::theTestRunGuideNamesTheBrowserCheckDocumentWithTheE2eSuites
---

# D-KNW-069 — A browser in a container reaches a site on the router

**Joining `ddev_default` is the whole of it, because the router carries each
project's hostname as a network alias.**

The other half of
[`D-KNW-068`](knw-068-looking-at-a-backend-change-is-a-suite-the-core-already-carries.md):
the prepared instance is a styleguide, and the defect that needs the developer's
own content still had nowhere to be seen.

## Evidence

Measured against the DDEV project running on this machine, which is the same
installation the report was written in.

- `docker port ddev-router` publishes 80 and 443 on `127.0.0.1` alone. A
  container started with `--add-host host.docker.internal:host-gateway`
  therefore reaches the host's gateway address and nothing answers: curl exits
  7, which is the connection refused the session reported.
- `--network ddev_default` alone answers 200 over HTTP for the project's own
  hostname, with no `--add-host` at all. The router's aliases on that network
  are its container name and every project hostname it serves, which is what
  resolves the name inside the container. The session's route — reading the
  router's address in that network and mapping the hostname onto it — also
  answers 200 and is what a hostname the resolver cannot answer still needs.
- HTTPS from a plain container fails verification: the certificate authority is
  installed on the host and not in the image. `ddev describe -j` carries
  `httpurl` beside `primary_url`, which is the way around it that changes no
  configuration.
- Node's resolution was checked directly: `require.resolve('@playwright/test')`
  from a directory outside the one holding `node_modules` throws, and resolves
  once a `node_modules` symlink sits beside it.
- The core checkout ignores `/typo3temp/*` and none of `Build/typo3temp/`, so an
  output path relative to the working directory the run started in lands in a
  committed directory.

## Decided

- It is a document rather than hints: what was missing is a procedure with an
  order to it, which is what
  [`D-FBK-043`](../feedback/fbk-043-a-structure-is-answered-with-a-document-rather-than-with-a-rule.md)
  answers with a document. `any/testing/browser-check`, because a DDEV site is
  reached the same way whoever is working on it.
- It carries the simple route first and the feedback's own as the case it is
  needed for. A wildcard additional hostname is not an alias Docker can answer,
  and that is the one place the router's address has to be read.
- `browser-tests` gains the words a reviewer actually uses — visual check,
  screenshot, render correctly, reproduce in the backend — because the session
  had that hint listed under `omittedHints` and skipped it: its own vocabulary
  was the vocabulary of somebody writing a spec.
- The document names no `-s` suite. Which suites exist is per branch and lives
  in `test-suite-hints.json`, which `KnowledgeTest` holds prose to.

## Assumed

- That the router's aliases are DDEV's behaviour rather than this machine's
  configuration. It was read from the running router and matches what the
  network is for; nothing here tried a second DDEV version.
- That the `Build` working directory is what a container run inherits. It is
  where the core's own npm scripts run and where the session saw its screenshots
  land, and this run started no browser container of its own.

## Wrong if

- A session on another DDEV version finds the hostname does not resolve on
  `ddev_default`. Then the alias is not the mechanism and the explicit mapping
  is the route, not the fallback.
- Somebody reaches for this document to write a committed browser suite. That is
  `project/testing/playwright`, and a `whenToUse` that lets the two be confused
  is the thing to fix.

## Since then

`feedback/2026-08-10-182417` was judged against this entry on 2026-08-11. It is
the session six hours after this document was written: a backend CSS patch on a
core checkout, which held the page's id from `typo3_project_describe` and never
opened it, and told its reader five times that it could not judge the change
visually.

The hints chosen above were re-run from that session's own paths.
`typo3_test_run_guide` returns `e2e-prepare` and `e2e-browser` for them and
names no page beside the two suites — it names `core/testing/scripts` under
`R-ANS-028` and stops there. `typo3_hint_lookup` with those paths selects the
`css` domain, where `browser-tests` is neither returned nor listed in
`availableHints`, so the `documents` block that carries this page comes back
empty. The page is named where the query already says what it is looking for:
"look at the change in a real browser" returns `browser-tests` with both
documents under it.

So `browser-tests` and `browser-tests-outside-core` deliver this page to a
caller who has decided that a browser is involved, and the reviewer of a visual
patch is the caller who has not. The page is here and did not reach the session,
which is the judging ladder's second step rather than its first, and it is
queued as
`todo/open/2026-08-11-081914-name-the-browser-check-page-where-the-e2e-suites-are.md`.

Two other placements were measured and not taken. Putting `css` on this hint's
domains makes it listable by a CSS task and still not returned, so the
`documents` block stays empty — which is why `D-KNW-017` put its crossing in the
hint the question reaches rather than in the domains. A review surface in
`typo3-core-patch-review` is the second, and it is the weaker first step: the
session did call `typo3_test_run_guide` and took `e2e-prepare` out of that
answer each time, so the answer it already read is the cheaper place to name the
page.

**Built on 2026-08-11.** `TestRunGuide` names the page under its suite blocks
wherever one of them is an e2e suite, in the form `R-ANS-028` asks for: a
`typo3_rule_lookup` call by `documentId`, with the styleguide instance's missing
content and the screenshot path as what the page carries and the answer does
not.
`KnowledgeTest::theTestRunGuideNamesTheBrowserCheckDocumentWithTheE2eSuites`
runs the CSS path the report was written from, and the functional case beside it
holds the block to the suites it is about.
