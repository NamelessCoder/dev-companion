---
date: 2026-08-03T16:28:36+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_configuration_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: seed a local TYPO3 14.3.5 development instance with demo content from a distribution packag...

## Observation

Task: seed a local TYPO3 14.3.5 development instance with demo content from a distribution package (typo3/theme-camino) so the extension can be clicked through against real pages. The import succeeds — pages, tt_content, fileadmin files and a site configuration all arrive — but the frontend answers 404 at the project root and the site is only reachable under /camino/. This looks like a property of the distribution and is not: it is unconditional core behaviour, and it took reading the importer to establish that.

TYPO3\CMS\Impexp\Import::processSiteConfigurations() writes, right before handing the configuration to SiteWriter:

    $configuration['rootPageId'] = (int)$importedRootPageId;
    $configuration['base'] = '/' . $targetIdentifier . '/';
    // @TODO Add error handling / routes etc where page ids are used and configured

The base is overwritten for every imported site configuration, discarding whatever the export carried — the value in the package's Initialisation/data.xml is irrelevant, and it happens to be /camino/ there only by coincidence. The reason is sound (the importer cannot know the domain of the target installation) but there is no event, no option and no override: the method is protected and called unconditionally from importData(). Combined with --create-site being inert in Composer mode whenever a distribution package is required, there is no supported way to have an installation seeded from a distribution come up on its own project URL. It has to be corrected afterwards — editing base in config/sites/<identifier>/config.yaml, or in the backend.

Two adjacent details found in the same code, both easy to misread as the cause: processSiteConfigurations() is skipped entirely when the importing backend user is not an admin, and it is also skipped when the package ships an Initialisation/Site/ directory, in which case ImportSiteConfigurationsOnPackageInitialization takes over via ImportExportUtility::disableSiteConfigurationImport(). typo3/theme-camino ships no Initialisation/Site/, only data.xml.

## Query

After seeding a TYPO3 14.3 installation from a distribution package (typo3/theme-camino) via "typo3 extension:setup", the frontend answers 404 at the project root and the site is reachable only at /<identifier>/.

## Suggestion

State plainly that a distribution imported through impexp always lands on base "/<identifier>/", regardless of the exported value, and that the project URL has to be set afterwards — with the pointer to Import::processSiteConfigurations() and the two conditions under which it does not run at all (non-admin importing user, package shipping Initialisation/Site/). Anyone seeding a development or demo installation from a distribution hits the resulting root-level 404 immediately and will otherwise look for the cause in the distribution package, where it is not.
