---
date: 2026-08-11T05:52:20+00:00
category: missing-knowledge
status: closed
closed: 2026-08-12
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# No hint covers backend routing internals: how a route carries its module, how sub-route identifie...

## Observation

Task: review Gerrit change 94686, which changes RecordBreadcrumbProvider::extractRouteIdentifier() in EXT:backend — it decides whether a breadcrumb node reuses the current request's route identifier or falls back to the module identifier.

The path-scoped call returned exactly one hint group, `system-extension-boundaries`, with three sentences ("keep changes inside the owning system extension", "reuse public APIs", "check nearby extension-local tests"). None bears on the diff. The `availableHints` list that came back is long, and reading it I picked `backend-modules` as the closest. That one returned module *registration* conventions: Modules.php keys, `routes` mapping a name to Controller::method, `_default`, `moduleData`, the labels domain, the 303-after-POST rule. Also nothing about the diff.

The three facts that actually decided the review, all read out of the checkout instead:
- A module route carries the module object in its `module` option, and a generic route registered in Configuration/Backend/Routes.php (record_edit) does not — that asymmetry is the whole fix. Read at ModuleRegistry.php:98-128 and Module.php:34-49.
- Sub-route identifiers are `<module>.<name>` for declarative modules and `<module>.<ControllerAlias>_<action>` for Extbase modules, and the Extbase form is generated per action with `'module' => $this` — ExtbaseModule.php:54-89.
- navigationComponent is inherited from the parent module by default, so `manage_search_index` under `content_status` under `content` is page-tree navigated although it declares nothing — BaseModule.php:96-104. This one is a trap: reading only Modules.php files, as I first did, gives the wrong answer for most modules.

There is also a fourth: there are two Route classes, TYPO3\CMS\Backend\Routing\Route (path, options) and TYPO3\CMS\Core\Routing\Route (path, defaults, requirements, options). The core test file under review had used the Core one and put the route identifier where $defaults goes, so getOption('_identifier') silently returned null and two functional tests had never reached the branch they were named after. I found that by writing a probe, not by lookup.

## Query

typo3_hint_lookup(task "backend breadcrumb provider route identifier for module nodes", paths ["typo3/sysext/backend/Classes/Breadcrumb/RecordBreadcrumbProvider.php"], targetVersion "15.0") — returned one hint group, id system-extension-boundaries. Then typo3_hint_lookup(id "backend-modules", targetVersion "15.0").

## Suggestion

Add a hint, id something like `backend-routing-internals`, matched by paths under typo3/sysext/backend/Classes/Routing/ and Classes/Module/ and by task words "route identifier", "sub-route", "module option", "breadcrumb": (a) module routes carry `module` in their options, routes from Routes.php do not, and ModuleResolver reads that option first; (b) sub-route identifier shapes for declarative vs Extbase modules, and that Extbase route options are module-wide with no per-action expression; (c) navigationComponent is inherited from the parent unless overridden, so a module's own registration does not answer whether it is page-tree navigated; (d) TYPO3\CMS\Backend\Routing\Route and TYPO3\CMS\Core\Routing\Route have different constructor signatures and mixing them makes getOption() return null without any error. Point (d) is worth stating in the `core-tests` hint too, since that is where it bit.
