---
date: 2026-07-29T16:19:01+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 06e8727
subject: "[FEATURE] Cover Extbase, and what breaks while writing a plugin"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# THERE IS NO EXTBASE HINT IN THE ARCHITECTURE CATALOG AT ALL.

## Observation

THERE IS NO EXTBASE HINT IN THE ARCHITECTURE CATALOG AT ALL.

51 hint ids, covering Backend CSS in nineteen separate entries, Backend TypeScript, FormEngine data providers, upgrade wizards, FAL, caches, console commands, site sets, TSconfig, frontend DataProcessors — and nothing on Extbase. Asking for it by task returns "datahandler-persistence", which is about DataHandler and says nothing about Extbase persistence; asking by id returns the list of what exists instead.

Extbase is a system extension, it is what typo3/cms-extbase is, and it is still the answer for anything a frontend needs beyond reading records: pagination, validated arguments, a query layer, forms. This session is a concrete example of both halves — I first built a product catalogue without Extbase (correctly, for a read-only view), and then the requirements grew to "viel mehr Daten, Pagination und Suche", at which point Extbase became the right tool and the server had nothing to say about it.

What a hint would have to carry, all of which I had to read out of the core sources:

- ExtensionUtility::configurePlugin($extensionName, $pluginName, array $controllerActions, array $nonCacheableControllerActions = [], ?string $pluginType = null) — the fifth parameter now throws unless omitted or 'CType'.
- ExtensionUtility::registerPlugin(..., string $flexForm = '') and the plugin signature it derives (strtolower(extensionName) . '_' . strtolower(pluginName)), because that signature is the CType, the TypoScript key, the FlexForm key and the argument namespace all at once.
- Configuration/Extbase/Persistence/Classes.php as the place to map a model onto a table that is not named by convention.
- Which paginator to use with what: QueryResultPaginator for an Extbase QueryResultInterface, QueryBuilderPaginator for a Doctrine QueryBuilder, ArrayPaginator for an array in memory — plus SlidingWindowPagination on top. The choice is not obvious and picking the wrong one silently paginates in PHP instead of in SQL.
- Cacheability: a list action that takes a search argument has to be in the nonCacheableControllerActions list, or the GET submission arrives without a cHash. That the two lists are per-action is what makes "list uncached, show cached" possible, and it is the single most consequential line in the whole registration.

Related gap in the same area: nothing describes when Extbase is and is not the right tool on v14, now that record-transformation and f:render.record cover read-only rendering without a model. That decision came up explicitly in this session and had to be argued from first principles.

## Query

typo3_architecture_lookup task="Extbase plugin in a project extension: domain model, repository, controller, plugin registration, FlexForm settings, persistence mapping to a custom table, pagination and search", targetVersion=14.3 → one hint returned, "datahandler-persistence". Then typo3_architecture_lookup id=extbase → no such hint; the id list has 51 entries and not one of them is about Extbase.

## Suggestion

Add an "extbase" architecture hint — plugin registration and the derived plugin signature, the persistence mapping file, controller and repository conventions, the paginator matrix, and the cacheable/non-cacheable action split. Add a second, short one on choosing between Extbase and the Record API on v14: records plus data processors for read-only rendering, Extbase once pagination, search, validated arguments or forms enter the picture. typo3_task_guide should route anything naming "plugin", "controller", "repository" or "pagination" to it.
