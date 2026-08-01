---
date: 2026-07-29T16:11:23+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 5229880
subject: "[FEATURE] Say how a content element and a container-resolved class are registered"
tool: typo3_architecture_lookup, typo3_component_lookup
directory: /home/benji/projects/site-new
---

# THREE v14 FACTS I HAD TO READ THE CORE SOURCE FOR, EACH OF WHICH FAILS AT RUNTIME IF GUESSED:

## Observation

THREE v14 FACTS I HAD TO READ THE CORE SOURCE FOR, EACH OF WHICH FAILS AT RUNTIME IF GUESSED:

1. A PageTitleProvider has to be `public: true` in Configuration/Services.yaml. PageTitleProviderManager resolves providers with $this->container->get($configuration['provider']), not with makeInstance, so the standard extension Services.yaml (_defaults public: false, one resource glob over Classes/) produces a "service not found" at request time. Nothing autoconfigures PageTitleProviderInterface — core/Configuration/Services.php registers autoconfiguration for SingletonInterface, MiddlewareInterface, RequestHandlerInterface and IconProviderInterface, but not for this one.

2. There is no TypoScript-only way to put a record's title into <title>. For a detail view rendered by a page template rather than by a controller — which is the natural shape on v14, see the other note — RecordTitleProvider is useless because nothing calls setTitle(). Writing a ~40-line PageTitleProvider is the answer, and it is not obvious that it is the answer.

3. ExtensionManagementUtility::addPlugin() has TWO parameters on v14: (array|SelectItem $itemArray, string $flexForm = ''). The $field and $extensionKey arguments are gone. The example everyone finds first is in Feature-102834 (v13.0), which shows addPlugin([...], 'CType', 'my_extension') — copying it into a v14 extension is a TypeError. Also undocumented anywhere I could find: addPlugin() seeds $GLOBALS['TCA']['tt_content']['types'][<value>] from the 'header' type as a stand-in, so a new content type silently gets the header element's field list until an explicit showitem replaces it.

None of this is reachable through the lookups. typo3_component_lookup covers backend markup, typo3_architecture_lookup covers subsystem conventions, and "how do I register X so the core actually finds it" falls between them.

## Query

no lookup answered this — searched the core sources by hand for PageTitleProviderManager and for the v14 signature of ExtensionManagementUtility::addPlugin()

## Suggestion

A "service registration" section in the architecture hints, listing the interfaces the core resolves through the container rather than through makeInstance and therefore need public: true — PageTitleProviderInterface is the one that bit here. Separately, the plugin/content-type hint should state the v14 addPlugin() signature explicitly and warn that the v13.0 changelog example no longer applies, plus the types-seeded-from-header behaviour. Feature-102834 is the most-linked page for this and it is a version trap.
