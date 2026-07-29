---
date: 2026-07-29T10:50:09+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Built a TYPO3 v14.3 business site from nothing. The single biggest architectural decision of the ...

## Observation

Built a TYPO3 v14.3 business site from nothing. The single biggest architectural decision of the whole build was how a sitepackage ships its initial content, and the server has nothing on it. The query above returned only the generic 'DataHandler and Persistence' hints ('changes are high-impact and usually need functional tests', 'preserve workspace, localization, permissions'), which are review criteria for a core patch, not guidance for the decision.

The decision space, all of it core mechanics and none of it covered:
1. Initialisation/data.xml (or .t3d) plus Initialisation/Site/<id>/ in the extension. Imported by ImportContentOnPackageInitialization and ImportSiteConfigurationsOnPackageInitialization on PackageInitializationEvent, i.e. by 'typo3 extension:setup'. Guarded by a sys_registry entry under namespace extensionDataImport, so it runs exactly once. Initialisation/Files copies into fileadmin/<extkey>.
2. typo3 impexp:import / impexp:export from the CLI, for repeat imports once the registry guard has fired.
3. Hand-written DataHandler seeding in a console command.

Non-obvious consequences an answer should carry:
- typo3/cms-impexp becomes a HARD requirement of the sitepackage. Without it CheckForImportRequirements only logs a warning and the site silently comes up empty. That is a trap worth naming explicitly.
- ImportSiteConfigurationsOnPackageInitialization remaps rootPageId to the imported page uid, but remaps nothing else. A shipped config.yaml whose 404 handler points at t3://page?uid=N keeps the stale N.
- It skips import entirely when the site identifier already exists in config/sites/, which silently changes behaviour between a fresh clone and an existing install.
- 'typo3 setup --create-site' and a shipped Initialisation/Site conflict: the former leaves a root page behind and makes the latter be skipped. Setup has to be run WITHOUT --create-site.

I initially went the DataHandler route because from an empty installation there is nothing to export, and hand-authoring T3D/XML is not practical. That reasoning is sound but the server never surfaced it, and it never surfaced that once content exists the export is a one-liner and the conventional artifact. A 'how does a sitepackage ship content' topic would have changed the build.

## Query

task="ship initial content with a sitepackage: impexp data.xml distribution initialisation versus DataHandler seeding"
