---
date: 2026-07-31T19:31:09+00:00
category: missing-knowledge
status: open
model: nemotron-3-ultra-free
tool: typo3_project_scope, typo3_extension_scope, typo3_architecture_lookup, typo3_changelog_lookup, typo3_label_lookup, typo3_icon_lookup
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. What I had to establish elsewhere (from checkout, own kn...

## Observation

Trimmed on 2026-08-02 to the two costs that are registrations. Six of the eight
were the contents of files this server does not read — TCA, Fluid, controllers,
eight test classes, `Initialisation/data.xml` — which is the boundary the report
itself calls reasonable. The `PageTitleProvider` rule was delivered, by the
`frontend-records` hint the same session's `typo3_architecture_lookup` calls both
returned; the PHP number is `D-ANS-011` and its runtime half is
`feedback/2026-07-31-193611`; and `settings.definitions.yaml` carries English
labels, unchanged since the day before the report. `D-ANS-015` has the readings.

Trimmed again on 2026-08-02 to the one registration that is left. The two
`registerPlugin()` identifiers are answered: `typo3_extension_scope` says which
of its content elements is an Extbase plugin and points at
`plugin.tx_<identifier>` where an element carries a `templateName`, so the
absence that produced finding #3 is gone. `D-ANS-018` has the readings.

What is left is that `Configuration/Form/Printworks/config.yaml` is reached by
nothing: `Extension::ROOT_FILES` is a fixed list of paths, while a form set is
discovered by its directory since v14.2 (#109412), the way site sets already are.

## Query

what I had to establish elsewhere in extension audit

## Suggestion

Consider adding a typo3_file_lookup or typo3_implementation_lookup for cross-file logic (e.g., "which TypoScript files are missing for registered CTypes", "which controllers lack PageTitleProvider"). But the current boundary (registration vs implementation) is reasonable.
