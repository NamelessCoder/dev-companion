---
date: 2026-07-31T17:27:57+00:00
category: idea
status: open
tool: typo3_extension_scope
directory: /home/benji/projects/bootstrap_package
---

# The two newest TYPO3 v14.3 extension deprecations (ext_tables.php present -> #109438; ext_emconf....

## Observation

The two newest TYPO3 v14.3 extension deprecations (ext_tables.php present -> #109438; ext_emconf.php present without composer.json version/providesPackages -> #108345) are exactly the kind of thing typo3_extension_scope could answer from files without booting: it already reads ext_localconf.php, ext_tables.php, ext_emconf.php and the registration files. The reviewer had to discover these through a functional-test run. A signal in extension_scope ('ships ext_tables.php: yes (deprecated v14.3)', 'composer.json missing version/providesPackages: yes') would surface the v15 migration surface in the scope call itself.

## Query

extension=bootstrap_package

## Suggestion

Extend the extension_scope answer with a short 'upgrade readiness' section read from files: presence of ext_tables.php, presence of ext_emconf.php, and whether composer.json declares extra.typo3/cms.version and Package.providesPackages — the exact predicates of deprecations #109438 and #108345.
