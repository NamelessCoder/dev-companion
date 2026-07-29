---
date: 2026-07-29T09:42:45+00:00
category: wrong-answer
status: open
tool: typo3_translation_domain_lookuptypo3_component_lookuptypo3_architecture_lookup
---

# The server has no notion of a target TYPO3 version. Every catalog and every hint is pinned to one...

## Observation

The server has no notion of a target TYPO3 version. Every catalog and every hint is pinned to one revision — typo3_catalog_scope reports branch main, version 15.0 — and answers are phrased as timeless fact. This project runs 13.4.31, which is the ordinary case outside core work, and it produces answers that are wrong for the caller. Concretely: typo3_translation_domain_lookup{path:"EXT:events_sitepackage/Resources/Private/Language/locallang_db.xlf"} returned domain "events_sitepackage.db" with no qualification. I verified against a core checkout that typo3/sysext/core/Classes/Localization/TranslationDomainResolver.php exists on main but is absent from origin/13.4, so translation domains do not exist in this project's version and TCA labels must still be written as the full LLL:EXT:... reference. Acting on that answer breaks every label it touches, and the failure is silent at build time. The tool's own description advertises this reach — "it also answers for a file outside the core" — which makes it the most likely of all these tools to be used from a site project, and therefore the most dangerous one to leave version-blind. typo3_component_lookup has the same exposure with a softer landing: the v15 markup and the --typo3-input-* custom property contract it returns do not match the v13.4 backend, though at least the payload carries the catalog block naming the revision. The architecture hints held up better — the IconSize enum, #[AsEventListener] and the final-ViewHelper shape are all valid on 13.4 — but that is luck of the draw, not a property the caller can rely on or check.

## Query

typo3_translation_domain_lookup{path:"EXT:events_sitepackage/Resources/Private/Language/locallang_db.xlf"} against a project pinned to typo3/cms-core ^13.4 (v13.4.31)

## Suggestion

Give every answer a version contract instead of leaving it implicit. Two things would fix most of it. First, accept an optional targetVersion argument on the version-sensitive tools and, when it does not match the catalog revision, either qualify the answer or decline it — typo3_translation_domain_lookup{path:..., targetVersion:"13.4"} should answer that translation domains were introduced after 13.4 and that the full LLL:EXT: reference is required, not hand back a v15 domain string. Second, carry the same catalog block that typo3_component_lookup already returns on every response, and add to each architecture hint the earliest version it holds for, so a caller can tell a convention that is stable across LTS versions from one that describes only main. Since the server already refuses to read the checkout, the version cannot be discovered — it has to be an argument, and typo3_server_scope should say that callers on an LTS branch are expected to pass it.
