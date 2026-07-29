---
date: 2026-07-29T09:46:14+00:00
category: tool-gap
status: open
tool: typo3_label_lookuptypo3_fluid_namespace_listtypo3_backend_module_lookuptypo3_configuration_lookup
---

# The console-backed tools need a fully migrated database, not merely an installed TYPO3, and a sit...

## Observation

The console-backed tools need a fully migrated database, not merely an installed TYPO3, and a site project spends a lot of its life not having one. With code fully installed and bin/typo3 --version answering "TYPO3 CMS 13.4.33", typo3_label_lookup and typo3_fluid_namespace_list both failed with a raw console stack trace: "An exception occurred while executing a query: Table 'db.tx_scheduler_task' doesn't exist". The schema had not been imported yet. Fresh clone before a database dump is restored, a colleague onboarding, CI, a container that is up but empty — in all of them the labels are sitting right there in the XLF files on disk and the tool cannot reach them. The contrast with typo3_icon_lookup makes the point: it reads the packages directly, needs no console, and was the single most useful thing this server did for me. Asked for content-element-map it answered "registered in EXT:events_sitepackage/Configuration/Icons.php" — my own project extension, correctly attributed. That is exactly what a site developer needs and it works without a database. There is no reason a label lookup should be strictly less available than an icon lookup when both are answered by files in the same packages. Two things this does well and should keep: the failure is honest rather than silent, and it passes the real console error through instead of flattening it, which is what let me diagnose the cause in one call.

## Query

typo3_label_lookup{query:"sponsor", extension:"events_sitepackage"} and typo3_fluid_namespace_list{} against an installed but not yet migrated TYPO3 13.4.33

## Suggestion

Give typo3_label_lookup a console-free fallback: it already enumerates the installed packages for the icon registry, so parse Resources/Private/Language/*.xlf from those same packages and search the trans-unit ids and source texts. Report which path answered, for example answeredBy:"packages" versus answeredBy:"console", and note in the payload that the file-based answer does not apply LANG/resourceOverrides, so the caller knows what the weaker answer is missing. typo3_fluid_namespace_list can be served the same way from the shipped defaults plus each package's ext_localconf.php registrations. typo3_configuration_lookup and typo3_backend_module_lookup genuinely need the assembled runtime state and should stay console-only, but their error should say that the database is not migrated rather than surfacing a bare SQL stack trace, since the remedy is a specific one.
