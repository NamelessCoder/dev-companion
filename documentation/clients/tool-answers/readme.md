# What the tools answered

Recorded on 2026-08-02 by `bin/cli tools:record`, over the calls
`Upkeep\ToolCalls` holds — the same ones `ToolContractTest` validates. One
page per tool, each answer whole. It is one run on one machine and it may be
older than the code: nothing checks it, and [tools.md](../tools.md) is where
the current shape of an answer is.

Answered against core-checkout, TYPO3 14.3.6-dev, the 14.3 core checkout below
.checkouts/, whose console could not be reached: <installation> has no TYPO3
console — none of bin/typo3, vendor/bin/typo3 exists. Half of these answers
belong to it rather than to this server — which labels and icons exist, what
the project consists of — and the other half would read the same anywhere.

Absolute paths are written as `<repository>`, `<installation>` and `<home>`,
because where a machine keeps its checkouts is not what these answers are
showing. Nothing else is rewritten: each block is what a client received.

- [`typo3_server_scope`](typo3_server_scope.md) — scope
- [`typo3_rule_lookup`](typo3_rule_lookup.md) — rules: hit, rules: miss
- [`typo3_script_lookup`](typo3_script_lookup.md) — scripts: hit, scripts: miss
- [`typo3_task_guide`](typo3_task_guide.md) — brief: with area, brief: task only, brief: paths of two kinds
- [`typo3_test_run_guide`](typo3_test_run_guide.md) — runTests: all, runTests: hit, runTests: miss, runTests: narrowed by paths
- [`typo3_architecture_lookup`](typo3_architecture_lookup.md) — architecture: path, architecture: topic, architecture: miss
- [`typo3_documentation_lookup`](typo3_documentation_lookup.md) — documentation: unsupported version
- [`typo3_component_lookup`](typo3_component_lookup.md) — components: list, components: hit, components: miss
- [`typo3_system_extension_lookup`](typo3_system_extension_lookup.md) — system extensions: hit, system extensions: miss, system extensions: everything
- [`typo3_reference_list`](typo3_reference_list.md) — references
- [`typo3_translation_domain_lookup`](typo3_translation_domain_lookup.md) — domain: EXT reference, domain: checkout path, domain: on an older target, domain: miss
- [`typo3_label_lookup`](typo3_label_lookup.md) — labels: hit, labels: miss
- [`typo3_icon_lookup`](typo3_icon_lookup.md) — icons: hit, icons: everything
- [`typo3_backend_module_lookup`](typo3_backend_module_lookup.md) — modules
- [`typo3_fluid_namespace_list`](typo3_fluid_namespace_list.md) — namespaces
- [`typo3_configuration_lookup`](typo3_configuration_lookup.md) — configuration
- [`typo3_schema_lookup`](typo3_schema_lookup.md) — schema: one table, schema: every table
- [`typo3_changelog_lookup`](typo3_changelog_lookup.md) — changelog: hit, changelog: swept by tag, changelog: miss
- [`typo3_project_scope`](typo3_project_scope.md) — project
- [`typo3_extension_scope`](typo3_extension_scope.md) — extension
- [`typo3_catalog_scope`](typo3_catalog_scope.md) — catalog scope
- [`typo3_commit_message_guide`](typo3_commit_message_guide.md) — commit: from parts, commit: from a message
