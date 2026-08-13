---
date: 2026-08-13T21:56:01+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# the ajax_ prefix is asymmetric across AjaxRoutes.php, buildUriFromRoute and TYPO3.settings.ajaxUr...

## Observation

Audit of an agent's own accumulated project notes against what this server answers, to delete what it covers and report what it does not. This is one of the entries that survived.

The note says one route name is written three different ways, and getting any of them wrong fails only at runtime:

- Configuration/Backend/AjaxRoutes.php registers the route WITHOUT the ajax_ prefix, e.g. the key is clearcache_flush.
- UriBuilder::buildUriFromRoute() must be called WITH it: buildUriFromRoute('ajax_clearcache_flush'). TYPO3 maps the ajax_-prefixed name back to the AjaxRoutes.php entry.
- TYPO3.settings.ajaxUrls in JavaScript/TypeScript uses the BARE name again: ajaxUrls.clearcache_page.

typo3_hint_lookup with "AJAX route registration AjaxRoutes UriBuilder buildUriFromRoute backend" returned backend-modules and backend-routing-internals. Between them they say a great deal about routes — the routes map, the _default entry, that "<module identifier>.<route name>" is how sub-routes are registered, that ModuleResolver reads the module option — and exactly one sentence about this file: "Configuration/Backend/Routes.php and AjaxRoutes.php declare backend routes outside a module, in the same declarative style." Nothing about the prefix, in any of the three places.

backend-routing-internals is otherwise precisely the hint this belongs in. It already carries the neighbouring naming rule ("The module's _default route is registered under the module identifier itself. Every other route of the module is registered as <module identifier>.<route name>") and it already warns about the failure shape this has — an option landing where defaults belong, getOption() returning null with no error. This is the same class of trap and it is not there.

The consequence is the ordinary one for declarative backend files, which backend-modules names itself: "a wrong key does not fail at boot, it fails when a user opens the module". Here it fails when a user clicks the thing, with a route-not-found, and the file that looks wrong is the one that is right.

## Query

typo3_hint_lookup(task: "AJAX route registration AjaxRoutes UriBuilder buildUriFromRoute backend")

## Suggestion

Add it to backend-routing-internals, as the three spellings side by side with the one line of rationale: the registration key is bare, buildUriFromRoute takes ajax_ and resolves it back, and TYPO3.settings.ajaxUrls is keyed bare again. Three lines, one example route carried through all three, which is the only way the asymmetry reads as a rule rather than as three unrelated facts.

Worth checking whether Configuration/Backend/Routes.php has an equivalent asymmetry, since the existing sentence pairs the two files and a reader will assume they behave alike.
