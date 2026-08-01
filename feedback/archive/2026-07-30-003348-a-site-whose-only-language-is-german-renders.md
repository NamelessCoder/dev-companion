---
date: 2026-07-30T00:33:48+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 90cb622
subject: "[FEATURE] Explain non-English site label setup"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# A site whose only language is German renders every core label in English, and nothing in this ser...

## Observation

A site whose only language is German renders every core label in English, and nothing in this server says why or what to do. Three things have to line up and none of them is discoverable from the site configuration a sitepackage ships: (1) SiteLanguage::getTypo3Language() derives the label language from the locale, and it derives "de_DE" from locale "de_DE.UTF-8" (Classes/Site/Entity/SiteLanguage.php:226-236) while TYPO3 ships its language packs under "de" — so the lookup misses and every label silently falls back to its English source, with nothing logged; the fix is an explicit `typo3Language: de` on the site language. (2) `typo3 language:update de` then still refuses with "Language iso code de not available or active", because LanguagePackService::getActiveLanguages() reads $GLOBALS['TYPO3_CONF_VARS']['LANG']['availableLocales'] (Classes/Localization/LanguagePackService.php:80-84), which a fresh Composer installation has empty — the locale has to be registered there first, e.g. `typo3 configuration:set LANG/availableLocales '["de"]' --json`. (3) Even with the pack in place, EXT:form's submit button still reads "Submit": the standard prototype's fallback label has no German translation, so a German form needs `renderingOptions.submitButtonLabel` spelled out in the form definition. Where this surfaces first in practice is a contact form on an otherwise fully German site — the validation messages come out as "This field is mandatory." next to hand-written German labels, which reads as a bug in the form rather than as missing configuration.

## Query

typo3_architecture_lookup task="Site settings ... site set" / typo3_task_guide on a German-language site — nothing covers how core labels reach the frontend in a non-English language

## Suggestion

Add this as its own hint — a non-English site and its labels — reachable from the site-configuration and site-set topics, and name it in the checklist of any task that sets up an installation or ships a site configuration. It needs the three steps in order (typo3Language on the site language, LANG/availableLocales before language:update, then the pack) with the reason the first one is not optional: a locale with a country code never matches a pack named after the language alone. Worth stating that the failure mode is a silent fallback to the English source rather than an error, so "the labels are English" is the only symptom, and worth pairing it with the EXT:form submitButtonLabel case as the concrete example of a label no pack will fix.
