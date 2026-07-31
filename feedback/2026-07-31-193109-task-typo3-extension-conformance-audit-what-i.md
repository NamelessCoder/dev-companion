---
date: 2026-07-31T19:31:09+00:00
category: missing-knowledge
status: open
model: nemotron-3-ultra-free
tool: typo3_project_scope, typo3_extension_scope, typo3_architecture_lookup, typo3_changelog_lookup, typo3_label_lookup, typo3_icon_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. What I had to establish elsewhere (from checkout, own kn...

## Observation

Task: TYPO3 extension conformance audit. What I had to establish elsewhere (from checkout, own knowledge, web) that the server should have answered:
1. Actual file contents — 20+ read() calls for TCA, TypoScript, Fluid templates, controllers, repositories, forms, tests, XLF files. The server provides registration metadata but not the implementation details.
2. Test coverage details — had to read ProductCatalogueTest.php, ProductRequestFormTest.php, HeroCarouselTest.php, EditorialContentElementsTest.php, ProductRepositoryTest.php, PrefillProductRequestFormTest.php, ProductTest.php, ProductDemandTest.php to assess quality surface.
3. Initialisation/data.xml content — the shipped base content (pages, content elements, products, hero slides) with uids, layouts, and relations. Critical for understanding test fixtures and editor workflow.
4. PHP version mismatch — discovered via bash, not from server. typo3_project_scope reported PHP ^8.4 from composer.json but actual runtime is 8.3.23.
5. Missing TypoScript files — Catalogue.typoscript and Teaser.typoscript don't exist (finding #3), confirmed by glob.
6. Form set registration — Configuration/Form/Printworks/config.yaml existence and content not verified by any lookup.
7. Settings.definitions.yaml labels — all German, no English source; server doesn't validate translation completeness.
8. PageTitleProvider absence — not detectable via lookups; had to trace from controller to route enhancer to realize it's missing.

The server excels at registration/runtime metadata and conventions; implementation details and cross-file logic still require reading the checkout.

## Query

what I had to establish elsewhere in extension audit

## Suggestion

Consider adding a typo3_file_lookup or typo3_implementation_lookup for cross-file logic (e.g., "which TypoScript files are missing for registered CTypes", "which controllers lack PageTitleProvider"). But the current boundary (registration vs implementation) is reasonable.
