---
date: 2026-07-28T14:54:36+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: ec515be
subject: "Name the mechanism in the DI, events, and FormEngine hints"
tool: typo3_architecture_hint
---

# Nothing in the knowledge base covers typo3/sysext/core/Configuration/DefaultConfiguration.php as ...

## Observation

Nothing in the knowledge base covers typo3/sysext/core/Configuration/DefaultConfiguration.php as such, and nothing covers how FormEngine FormDataProviders are ordered. The path only matched generic PHP sections. Yet this is exactly where a whole class of FormEngine bugfixes lands: $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['formDataGroup'][...] declares each provider with a "depends" and "before" list, and the ordering is the fix. The real patch for the bug I tested (99669172ad8) consisted of adding PageTsConfigMerged to the "depends" array of two formDataGroup entries — an agent that does not know this graph exists will instead go and patch the provider class itself, which is the wrong fix.

## Query

paths=["typo3/sysext/core/Configuration/DefaultConfiguration.php"] — FormDataProvider ordering

## Suggestion

Add an architecture hint keyed on typo3/sysext/core/Configuration/DefaultConfiguration.php (and on the topic "FormDataProvider", "formDataGroup", "tcaDatabaseRecord") that explains: providers are registered per formDataGroup with "depends" and "before"; a provider that reads merged page TSconfig must depend on PageTsConfigMerged; changing DefaultConfiguration.php is a runtime-configuration change that needs functional coverage rather than a unit test; and the group names (tcaDatabaseRecord, tcaSelectTreeAjaxFieldData, flexFormSegment, ...). More generally: DefaultConfiguration.php is the default of TYPO3_CONF_VARS and edits there affect every installation, so they belong in the patch description.
