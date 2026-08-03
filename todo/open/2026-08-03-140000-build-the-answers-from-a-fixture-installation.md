# Build the installation answers from a fixture

**Serves:** documentation/clients/tools/
**Priority:** normal

Nine tools declare `answeredBy`, and their pages can only be written on a
machine that has a booted TYPO3: `typo3_label_lookup`,
`typo3_fluid_namespace_list`, `typo3_configuration_lookup`,
`typo3_schema_lookup`, `typo3_backend_module_lookup`, `typo3_icon_lookup`,
`typo3_changelog_lookup`, `typo3_project_scope`, `typo3_extension_scope`. That
is why three of them show no such answer today.

A page documents the endpoint and the states it answers in, not what one machine
returned on one day. So the answer under a state may be produced rather than
recorded, and `tests/Support/FakeInstallation.php` is the proof it can be: it
writes a `vendor/autoload.php` that the real probe really boots into, really
returning icons, TCA and content elements. What it does not yet cover is the
console — `typo3_label_lookup` runs `language:domain:search` through
`Typo3Cli::run` — and everything goes through `Typo3Cli::execute`, which is one
seam.

The step: lift the fake out of `tests/Support/` into an installation this
repository writes, shaped per state rather than after a real site, and let the
pages be built against it. Then `tools:check` holds the whole page instead of
its upper half, the docs build with no DDEV and no network, and
`bin/cli tools:record` is left with the three tools that reach outside
(`D-DOC-008`).

Settle before writing it:

- Where the fixture lives, and whether it is committed or built by a command the
  way `.checkouts/` is.
- What a state is, and where it is declared. `Upkeep\ToolCalls` already keys
  every call by one — "rules: hit", "labels: miss" — so the table may already be
  the list, or the list may belong beside the tool.
- Whether an answer produced from a fixture may sit under the same heading as
  one recorded from a real installation, or whether the page has to say which of
  the two a reader is looking at. It has to say it: a fixture answer is true of
  the fixture, and `D-DOC-006` is why that distinction is not decoration.
