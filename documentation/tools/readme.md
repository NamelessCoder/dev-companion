# The tool surface

Every tool this server offers, one page each. A page says what the tool is
called, what it takes, the fields it answers with and what a call to it comes
back with, and this is the list of them.

The half of a page above its `## Answered` heading is written by `bin/cli
tools:index` from the classes that answer the calls, and `bin/cli tools:check`
fails where it has gone stale — a surface written out a second time by hand
stops describing the answer at the first change nobody carried across. What is
below that heading is one of two things, and the sentence it opens with says
which. Where a tool's answers read nothing an installation contains, they are
derived and held by that same check. Where they do, an installation has to be
called for them: `bin/cli tools:record` writes those and nothing checks them, so
such a page may say what it answered on a day the code has since moved past. Two
tools have no answered half at all, on purpose, and say so in its place.

A client may be offered fewer than these. `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS` names the
tools a caller does not want offered, the two feedback tools exist only in a
standalone checkout, and `typo3_server_scope` names what was left out.

The schema on a page is YAML: a key per field, the fields of an object or of a
list entry nested under it, and the value is the type. A field carries
`# optional` where it may be absent, because required is the promise — a
required output field is present on every path through the tool, misses
included. Absolute paths in a recorded answer are written as `<repository>`,
`<installation>` and `<home>`, so no page carries one machine's layout.

Each page names the sources that can answer that tool, under its annotations and
at the foot of its description, and links them into
[Where an answer comes from](answer-sources.md) — which is the same statement
read the other way round, one heading per source with the tools it answers. What
it settles is not what a tool is about but whether it can be asked at all in the
state the machine is in.

The [readme](../../readme.md) groups the tools by where their answers come from;
a page here says what one tool is for, what goes in and what shape comes back.

- [`typo3_server_scope`](typo3_server_scope.md) — Orientation for this server.
- [`typo3_rule_lookup`](typo3_rule_lookup.md) — Search the TYPO3 rules and
  procedures this server carries, by topic.
- [`typo3_script_lookup`](typo3_script_lookup.md) — Find notes for TYPO3 core
  scripts and commands.
- [`typo3_task_guide`](typo3_task_guide.md) — Build a task checklist enriched
  with matching hints and relevant core checks.
- [`typo3_test_run_guide`](typo3_test_run_guide.md) — Say what this core
  checkout needs before a test can run at all, and which
  Build/Scripts/runTests.sh commands to run once it can.
- [`typo3_hint_lookup`](typo3_hint_lookup.md) — Return hints for TYPO3 core
  paths or task topics, grouped by section.
- [`typo3_documentation_lookup`](typo3_documentation_lookup.md) — Search or read
  the official live TYPO3 documentation for a covered TYPO3 line.
- [`typo3_forge_lookup`](typo3_forge_lookup.md) — Read the TYPO3 issue tracker
  at forge.typo3.org before writing a patch.
- [`typo3_gerrit_lookup`](typo3_gerrit_lookup.md) — Find out whether a TYPO3
  core patch already exists, from the review server at review.typo3.org.
- [`typo3_component_lookup`](typo3_component_lookup.md) — Look up TYPO3 backend
  UI components by name or topic.
- [`typo3_system_extension_lookup`](typo3_system_extension_lookup.md) — Answer
  whether an extension is part of the TYPO3 core, and on which versions.
- [`typo3_reference_list`](typo3_reference_list.md) — List the worked examples
  the TYPO3 core ships of its own conventions, and what each one is a reference
  for.
- [`typo3_translation_domain_lookup`](typo3_translation_domain_lookup.md) —
  Compute the translation domain an XLF file resolves to, from its path.
- [`typo3_label_lookup`](typo3_label_lookup.md) — Search the labels registered
  in the TYPO3 installation you are working in.
- [`typo3_fluid_namespace_list`](typo3_fluid_namespace_list.md) — List the Fluid
  ViewHelper namespaces that are globally available in the TYPO3 installation
  you are working in, so a template knows which prefixes it may use without
  declaring them.
- [`typo3_configuration_lookup`](typo3_configuration_lookup.md) — Read an
  effective TYPO3_CONF_VARS value from the installation you are working in.
- [`typo3_schema_lookup`](typo3_schema_lookup.md) — List the columns TYPO3
  derives for a table from its TCA.
- [`typo3_backend_module_lookup`](typo3_backend_module_lookup.md) — List the
  backend modules registered in the TYPO3 installation you are working in, with
  the extension that declares each one, its place in the module tree, its labels
  and its route.
- [`typo3_icon_lookup`](typo3_icon_lookup.md) — Validate or find an icon
  identifier in the TYPO3 backend icon registry of the installation you are
  working in.
- [`typo3_changelog_lookup`](typo3_changelog_lookup.md) — Search the TYPO3
  changelog of the installation you are working in.
- [`typo3_project_describe`](typo3_project_describe.md) — Describe the project
  around the TYPO3 installation this server was started in.
- [`typo3_extension_describe`](typo3_extension_describe.md) — Describe what one
  installed extension registers.
- [`typo3_catalog_scope`](typo3_catalog_scope.md) — Report whether component
  contracts come from the active installation or the bundled fallback, which
  TYPO3 core revision the fallback catalogs were taken from, what they cover,
  and how to re-check them.
- [`typo3_commit_message_guide`](typo3_commit_message_guide.md) — Draft and
  check a TYPO3 commit message.
- [`typo3_feedback_record`](typo3_feedback_record.md) — Leave feedback about a
  gap, wrong answer, or missing capability of this knowledge server.
- [`typo3_feedback_list`](typo3_feedback_list.md) — List improvement feedback
  recorded via typo3_feedback_record, newest first, so they can be worked off.
