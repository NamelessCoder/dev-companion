---
date: 2026-08-11T05:52:42+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_backend_module_lookup
directory: /home/benji/projects/typo3-cms
---

# Never called typo3_backend_module_lookup while hand-parsing every Modules.php; its name reads as ...

## Observation

Task: review Gerrit change 94686. The patch's stated guarantee was "a page-tree navigated module parameterizes every sub-route solely by the page id". To test that claim I needed two things about the installation: which backend modules are navigated by the page tree *including inheritance*, and which of them have routes beyond `_default`.

typo3_backend_module_lookup was in the deferred-tool list for the whole session. I never loaded its schema. From the name I expected "describe one module I can already name" — a validation tool like typo3_icon_lookup — not "enumerate what is registered, with routes and effective navigation component". So I never formed the query.

What I did instead cost three steps and a wrong intermediate answer:
1. `grep -rn "page-tree-element" --include=Modules.php` over all sysexts. Six hits. I took that as the set of page-tree modules — wrong, because BaseModule::getNavigationComponent() inherits from the parent, so `content` declaring it makes web_layout, records, content_status, manage_search_index, recycler and more page-tree navigated without declaring anything. I only caught this because a later reading of BaseModule.php:96-104 contradicted the grep.
2. A `php -r` loop including every Configuration/Backend/Modules.php to list sub-routes. It fatal-errored on typo3/sysext/backend/Configuration/Backend/Modules.php with `Uncaught Error: Class "TYPO3\CMS\Backend\View\PageViewMode" not found`, because that file references enum constants and cannot be included outside a booted core. I read that one file by hand.
3. A functional-test probe to confirm that manage_search_index really inherits '@typo3/backend/tree/page-tree-element' and that its Extbase sub-routes carry the module option.

The assumption that made me skip the tool — "it answers about one named module" — is the one I would like corrected, and I would make the same skip next session on the same name.

## Query

Not called. What I ran instead, in the checkout: `for f in $(ls typo3/sysext/*/Configuration/Backend/Modules.php); do php -r '$m = include $argv[1]; ...' "$f"; done` to list modules with sub-routes; `grep -rn "page-tree-element" --include=Modules.php` to find page-tree modules; then Read on BaseModule.php:96-104 to learn the value is inherited, which invalidated the grep result.

## Suggestion

The tool gap: an installation-read enumeration of modules with their registered routes and effective navigationComponent. It is the answer a `php -r` over Modules.php cannot produce, both because inheritance is resolved at runtime and because EXT:backend's own Modules.php references enum constants and cannot be included standalone.

The description half is answered — D-ANS-077, 2026-08-12. It has opened with "List the backend modules registered in the TYPO3 installation you are working in" since 2026-08-01, ten days before this report, and the name keeps `lookup` because that is the verb an enumeration answering an optional query carries here.
