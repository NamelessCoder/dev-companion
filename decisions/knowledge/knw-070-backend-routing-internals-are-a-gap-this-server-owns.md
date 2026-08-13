---
id: D-KNW-070
date: 2026-08-12
status: open
---

# D-KNW-070 — Backend routing internals are a gap this server owns

**How a backend route carries its module, what a sub-route identifier looks
like, and that `navigationComponent` is inherited are inside this server's
boundary and missing from it.**

The corpus answers how a module is registered and how an HTTP request is
handled, and nothing in between. A session reviewing a change to the routing
itself is handed the registration conventions, which do not bear on the diff.

## Evidence

- Re-run on 2026-08-12 against the corpus as it is now. `bin/cli hints:probe`
  with the feedback's own task — "backend breadcrumb provider route identifier
  for module nodes" — matches nothing and returns all 85 hints as the index. The
  same probe on the words the session needed reaches `extension-boot-files` on
  text alone, which is about `ext_localconf.php`.
- The vocabulary is absent. `navigationComponent`, `sub-route`,
  `ModuleRegistry`, `ExtbaseModule`, `_identifier` and `page-tree-element` occur
  nowhere below `knowledge/` or `skills/`.
- The two neighbouring hints are each about something else. `backend-modules`
  answers what a `Configuration/Backend/Modules.php` declares — the keys, the
  `routes` map, `_default`, `moduleData`, the labels domain — and is matched by
  those file names. `routing-request-handling` matches `/Routing/` and states
  PSR-7, middleware registration and request scope.
- The path-scoped call the session actually made returned
  `system-extension-boundaries`, whose `appliesTo` is `typo3/sysext/`. That is
  what a path under `backend/Classes/Breadcrumb/` reaches today: the hint that
  applies to every core file.
- The session read the four facts out of the checkout instead, and names where:
  `ModuleRegistry.php`, `Module.php`, `ExtbaseModule.php` and
  `BaseModule.php:96-104`. The fourth — two `Route` classes with different
  constructors, so a route identifier passed where `$defaults` goes makes
  `getOption('_identifier')` return null — it found by writing a probe, and two
  functional tests had never reached the branch they were named after.

## Decided

- Built, as a hint of its own rather than more sentences on `backend-modules`.
  One hint is one question (`D-KNW-030`): that one answers how a module is
  declared, this one what the router does with it at runtime, and they are
  reached by different paths — `Classes/Routing/`, `Classes/Module/` and the
  callers of both, against declarative file names.
- The boundary is what the core's own classes do with a route: the `module`
  option a module route carries and a `Routes.php` route does not, the two
  sub-route identifier shapes, the inheritance of `navigationComponent`, and the
  two `Route` classes. Registration stays with `backend-modules` and request
  handling with `routing-request-handling`.
- The `Route` confusion goes to `core-tests` as well, because a test file is
  where it bit and where the silence is total: the wrong class makes
  `getOption()` return null with no error, and the test passes while asserting
  nothing.
- What any of it says about TYPO3 waits for the reading. This judgement read
  this repository and nothing else, so the statements are established against
  `.checkouts/` on all four covered lines and bound there — which is the todo's
  first step and not a sentence copied out of the feedback.

## Assumed

- That the four facts hold widely enough to be one hint. The session read them
  on the development line, and a fact that turns out to be one major's is a
  `since` rather than a statement.
- That a review session reaches this by path. The call that missed was
  path-scoped, so the `appliesTo` paths are what decide whether the hint arrives
  at all.

## Wrong if

- A session with the hint installed still goes to the checkout for the same
  facts, which would say the gap was routing rather than knowledge.
- `typo3_backend_module_lookup` turns out to answer the resolved
  `navigationComponent` from the installation, which would make that one an
  answer rather than a statement — `feedback/2026-08-11-055242` is the report
  that asks it, and it is judged on its own card.
- The reading finds each fact bound to a different major, so the hint is a table
  of versions rather than four sentences a reviewer can hold.

## Since then

The second **Wrong if** was read on 2026-08-12 and does not hold.
`typo3_backend_module_lookup` answers the six CSV columns of
`debug:backend:modules`, which carry neither the declared nor the resolved
`navigationComponent`, so the inheritance stays a statement and the hint stays
the answer to it. What the tool should carry instead — the resolved component
and the routes, for the installation in front of the caller — is `D-ANS-077`.

`feedback/2026-08-13-215601` is a second identifier shape inside this boundary
and it was not enumerated: an `AjaxRoutes.php` route is written three ways, and
the hint says none of them. It is not the first **Wrong if**. That one describes
a session going to the checkout for a fact the hint carries, and this one went
for a fact it does not — so the boundary held and the enumeration under it was
short. Nor is it routing: `bin/cli hints:probe` with "AjaxRoutes ajaxUrls route
identifier prefix" ranks `backend-routing-internals` first on its `appliesTo`,
which is the hint arriving with nothing to say.

What the enumeration was short by is one mechanism rather than one sentence.
`AbstractServiceProvider::configureBackendRoutes()` merges `Routes.php` under
the keys the file writes and registers every `AjaxRoutes.php` entry under
`'ajax_' . $routeIdentifier`, so the registration key is bare,
`buildUriFromRoute` takes the prefixed name, and
`PageRenderer::addAjaxUrlsToInlineSettings()` strips the prefix again for
`TYPO3.settings.ajaxUrls`. Read on 12.4, 13.4, 14.3 and main; nothing is bound.
`Routes.php` has no such asymmetry, which is the half `backend-modules` gets
wrong by pairing the two files as "the same declarative style" — the general
sentence written before the exception existed.

The enumeration was extended on 2026-08-14: `backend-routing-internals` states
the mechanism and carries `page_tree_data` through all three spellings,
`backend-modules` qualifies the pairing, and the reporting session's own query
now ranks the routing hint first on `Configuration/Backend/AjaxRoutes.php`,
`buildUriFromRoute` and `ajaxUrls` in its `appliesTo`.
