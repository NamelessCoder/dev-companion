---
date: 2026-07-30T00:01:13+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 09faef5
subject: "[FEATURE] Explain which site settings source wins"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# The precedence between the two places a site's own settings can live is not in the answer, and it...

## Observation

The precedence between the two places a site's own settings can live is not in the answer, and it is the one thing about them that surprises. The site-sets hint explains settings.definitions.yaml and the set's settings.yaml well, and the project-repository-layout hint distinguishes config/sites/<id>/config.yaml from what a sitepackage ships in Initialisation/Site/<id>/ — but neither says what happens when a site has settings inline under the `settings:` key of config.yaml *and* a config/sites/<id>/settings.yaml. It is not a merge: SiteSettingsFactory::createSettings() does `$rawSettings = $this->loadLocalSettings($siteIdentifier) ?? $inlineSettings` (typo3/cms-core 14.3, Classes/Site/SiteSettingsFactory.php:100-108), so the existence of settings.yaml discards the inline block wholesale. The failure mode is quiet and nasty: a site whose settings sit inline in config.yaml — which is what a shipped Initialisation/Site/ seed produces — loses every one of them the moment anything creates a settings.yaml, and the backend's settings editor writes exactly that file (SiteSettingsService names it "our persistence target"). I had to read the core source to establish this, on a question that is squarely a conventions question.

## Query

typo3_architecture_lookup task="Site settings: settings.yaml of a site versus the inline settings key in config.yaml, and settings shipped by a site set", paths=["config/sites/main/config.yaml","config/sites/main/settings.yaml","Configuration/Sets/Printworks/settings.definitions.yaml"]

## Suggestion

State it in the site-settings/site-sets area: config/sites/<identifier>/settings.yaml and the inline `settings:` key of config.yaml are alternatives, not layers — settings.yaml replaces the inline block instead of merging with it, and set defaults are what either one is resolved against. Name the consequence, because it is what bites: editing site settings in the backend persists them to settings.yaml, so a site that carried its settings inline in config.yaml is silently emptied of them at that moment. The advice that follows is to pick one place per site and, for a sitepackage that ships Initialisation/Site/<id>/config.yaml with an inline settings block, to know that the first backend save moves the truth to a different file.
