---
date: 2026-07-29T16:03:37+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: a481028
subject: "[FEATURE] Answer how records of an own table reach a page"
tool: typo3_task_guide, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# SCENARIO (recorded in full so it can be turned into a guide):

## Observation

SCENARIO (recorded in full so it can be turned into a guide):

A TYPO3 v14.3 composer project with exactly one project extension, a sitepackage. Frontend is site set + PAGEVIEW + Fluid, no PHP, no Extbase. Task: add a product section — list page, detail page, and a teaser element of products on the homepage.

The solution that actually works on v14.3, none of which the server suggested:

1. Records, not Extbase. Configuration/TCA/tx_printworks_product.php only; no ext_tables.sql (auto columns since #101553), no model, no repository, no controller.
2. List and detail are ONE page. A page layout "products" renders Pages/Products.fluid.html; setup.typoscript always fills {products} and fills {productDetail} only when the request carries the argument, guarded by if.isTrue.data = GP:product. So /produkte and /produkte/<slug> are the same page.
3. Reading: dataProcessing "database-query" with a nested "record-transformation", plus "files" for the images and "comma-separated-value" for a Label|Wert text field. uidInList.data = GP:product is safe because getWhere() runs it through GeneralUtility::intExplode().
4. URLs: Configuration/Sets/Printworks/route-enhancers.yaml, type Simple with a PersistedAliasMapper on the slug, and limitToPages as an Expression Language string matching page["backend_layout"]. That combination (#107837 v14.1 plus #109263 v14.2) is what lets a site set ship routing WITHOUT a page uid in it — which matters because a shipped site configuration only gets rootPageId remapped on import. This is the single most useful v14 fact for this whole task and the server never mentions it.
5. Teaser: a CType (list_type is gone in v14), registered with ExtensionManagementUtility::addPlugin() plus addTcaSelectItemGroup(), rendered through lib.contentElement.
6. The <title> of the detail view needs a PageTitleProvider — see the separate note.

The task_guide answer for this was two architecture hints (sitepackage-initial-content, tca-formengine) and a checklist written for a core patch ("Confirm the target branch and the issue context of this repository", "Add functional coverage for TCA processing", "Run targeted tests first; broaden to CGL, functional"). None of that applies to a sitepackage, and the parts that would have helped were absent.

## Query

typo3_task_guide task="Add a product list and product detail rendering plus a product teaser element to a sitepackage extension: custom database table, TCA, frontend plugin/content elements, routing for the detail view", changeType=feature, targetVersion=14.3, area=printworks_sitepackage

## Suggestion

Either serve a real answer for this shape of work or say clearly that it is out of scope, rather than returning a core-patch checklist with the core-only items filtered out. If it is in scope, an architecture hint like "frontend-records" would carry: records vs Extbase on v14, database-query + record-transformation, the single-view-from-a-request-argument pattern, route-enhancers.yaml in a site set with an Expression Language limitToPages, and why a set must not contain page uids. typo3_architecture_lookup has "frontend-page-rendering" and "site-sets" hints that stop exactly where this task begins.
