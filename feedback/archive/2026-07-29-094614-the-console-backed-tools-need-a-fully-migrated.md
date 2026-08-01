---
date: 2026-07-29T09:46:14+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: 756dd7e
subject: "Read the Fluid namespaces from the packages that declare them"
tool: typo3_fluid_namespace_list
---

# typo3_fluid_namespace_list still needs a database it does not use

## Observation

Two thirds of this note are closed: `typo3_label_lookup` now reads the XLF files
of the installed packages when the console cannot be reached and reports
`answeredBy: "packages"`, and a console that fails on a missing table is
diagnosed as a database without a schema rather than passed through as a stack
trace.

What remains is `typo3_fluid_namespace_list`. With code fully installed and
`bin/typo3 --version` answering "TYPO3 CMS 13.4.33" it failed the same way,
although what it answers — the shipped default namespaces plus what each package
registers — is on disk like the labels are.

## Query

typo3_fluid_namespace_list{} against an installed but not yet migrated TYPO3 13.4.33

## Suggestion

Serve it from the shipped defaults plus each package's `ext_localconf.php`
registrations, the way the labels and the icon registry are served, and report
`answeredBy: "packages"` for that path. Note that a registration file is
ordinary PHP: `InstalledIcons` parses rather than includes it, and this would
have to do the same.
