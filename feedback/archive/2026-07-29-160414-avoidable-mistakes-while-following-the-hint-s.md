---
date: 2026-07-29T16:04:14+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 434b5c2
subject: "[TASK] Say how to get a DataHandler, since the hint asks for one"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# AVOIDABLE MISTAKES while following the hint's own advice. The "sitepackage-initial-content" hint ...

## Observation

AVOIDABLE MISTAKES while following the hint's own advice. The "sitepackage-initial-content" hint ends with exactly the right instruction — "which is also the way in for the first build, where there is nothing to export yet and hand-writing T3D is not an option: seed with DataHandler, then export" — but says nothing about how to get a DataHandler in the first place. TYPO3 offers no console command for it, so it means a throwaway bootstrap script, and two of its three steps are traps:

1. Copying the bootstrap from vendor/typo3/cms-cli/typo3 gives you Bootstrap::init($classLoader, true). The second argument is $failsafe, not "yes please boot properly". In failsafe mode the DI container is incomplete and the first thing that touches TcaSchemaFactory dies with "Too few arguments to function TcaSchemaFactory::__construct(), 0 passed". The CLI binary can pass true because CommandApplication finishes the boot afterwards; a hand-written script has to call Bootstrap::init($classLoader) without it.

2. Bootstrap::initializeBackendAuthentication() alone fails with "Undefined global variable $BE_USER". CommandApplication calls Bootstrap::initializeBackendUser(CommandLineUserAuthentication::class) first. Both are needed, in that order.

3. Anything that reaches Site::getAttribute() also needs $GLOBALS['LANG'], which CommandApplication sets from LanguageServiceFactory::createFromUserPreferences($GLOBALS['BE_USER']).

Also worth stating: cross-table NEW placeholders in one datamap are not worth relying on. Creating the pages in a first process_datamap() run, reading substNEWwithIDs, and creating the records with real pids in a second run is the version that works predictably.

And the export command needs every custom table named explicitly: impexp:export --table=tt_content --table=tx_printworks_product. A record table left out of that list is silently missing from data.xml.

## Query

typo3_architecture_lookup id=sitepackage-initial-content — "seed with DataHandler, then export"

## Suggestion

Extend the sitepackage-initial-content hint with the concrete seeding recipe: Bootstrap::init($classLoader) WITHOUT the failsafe flag (and why the CLI binary passes true), then initializeBackendUser(CommandLineUserAuthentication::class), then initializeBackendAuthentication(), then $GLOBALS['LANG']. Plus: one --table option per custom table on impexp:export, and the two-pass datamap for pages-then-records.
