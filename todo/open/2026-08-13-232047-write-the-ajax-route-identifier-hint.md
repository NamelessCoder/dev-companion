# Write the ajax route identifier hint

**Serves:** feedback/2026-08-13-215601-the-ajax-prefix-is-asymmetric-across-ajaxroutes.md
**Priority:** normal

Judged as `D-KNW-070`: step 1a, and inside the boundary that entry already drew
— what the core's own classes do with a route — so it is sentences on
`backend-routing-internals` rather than a hint of its own. `normal` because the
reading is done and the entry it goes into exists, not because more than one
session reported it; one did.

The judging run established all of it against `.checkouts/`, on 12.4, 13.4, 14.3
and main, so nothing is bound and nothing here needs reading again:

- `AbstractServiceProvider::configureBackendRoutes()` merges
  `Configuration/Backend/Routes.php` under the keys that file writes, and
  registers every `Configuration/Backend/AjaxRoutes.php` entry under
  `'ajax_' . $routeIdentifier` with `ajax` set on its options — 13.4 line 117.
- So `buildUriFromRoute()` takes the prefixed name for an AJAX route and the
  bare key for a `Routes.php` one. `TreeController` calls
  `buildUriFromRoute('ajax_page_tree_data')` against a `page_tree_data` entry.
- `PageRenderer::addAjaxUrlsToInlineSettings()` strips the prefix back off for
  `TYPO3.settings.ajaxUrls`, so JavaScript reads `ajaxUrls.page_tree_data`. 14.3
  and main strip a leading `ajax_`; 12.4 and 13.4 `str_replace` it. The
  difference reaches no identifier the core registers and is not worth stating.

Carry one example route through all three spellings, which is what makes it a
rule rather than three facts. Say that `Routes.php` has no such asymmetry: the
feedback asks it, and `backend-modules` currently pairs the two files as "the
same declarative style", which is the sentence a reader is misled by. That
sentence needs the qualifier in the same commit.

Then add `Configuration/Backend/AjaxRoutes.php`, `ajaxUrls` and
`buildUriFromRoute` to the `appliesTo` of `backend-routing-internals`, and run
`bin/cli hints:probe` with the feedback's own task — "AJAX route registration
AjaxRoutes UriBuilder buildUriFromRoute backend" — which returns
`backend-modules` first today. Archive the feedback in the same commit.
