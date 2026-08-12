:navigation-title: Tools

The tool surface
================

Every tool this server offers, one page each. A page says what the tool is
called, what it takes, the fields it answers with and what a call to it comes
back with, and this is the list of them.

Every tool answers twice: the readable text, and the same answer as
``structuredContent`` matching the ``outputSchema`` the tool declares — matches with
their source, coverage and score, checks as command strings, components, icons
and labels as typed records, commit diagnostics as ``level``/``code``/``message``. So
composing several tools does not mean parsing headings and code fences back out
of prose. All tools are annotated ``readOnlyHint``; only ``typo3_feedback_record``
writes anything, and then only a new file.

Names are ``typo3_<subject>_<verb>``, with the verb taken from a fixed set —
``lookup`` finds and may find nothing, ``guide`` composes an answer for a task,
``list`` enumerates, ``scope`` states what a source covers, ``describe`` states what
one thing you name is, ``record`` writes. So the name already says what shape the
answer has.

The half of a page above its ``Answered`` heading is written by ``bin/cli
tools:index`` from the classes that answer the calls, and ``bin/cli tools:check``
fails where it has gone stale — a surface written out a second time by hand
stops describing the answer at the first change nobody carried across. What is
below that heading is one of two things, and the sentence it opens with says
which. Where a tool's answers read nothing an installation contains, they are
derived and held by that same check. Where they do, an installation has to be
called for them: ``bin/cli tools:record`` writes those and nothing checks them, so
such a page may say what it answered on a day the code has since moved past. Two
tools have no answered half at all, on purpose, and say so in its place.

A client may be offered fewer than these. ``TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`` names the
tools a caller does not want offered, the two feedback tools exist only in a
standalone checkout, and ``typo3_server_scope`` names what was left out.

The schema on a page is YAML: a key per field, the fields of an object or of a
list entry nested under it, and the value is the type. A field carries
``# optional`` where it may be absent, because required is the promise — a
required output field is present on every path through the tool, misses
included. Absolute paths in a recorded answer are written as ``<repository>``,
``<installation>`` and ``<home>``, so no page carries one machine's layout.

Each page names the sources that can answer that tool, under its annotations and
at the foot of its description, and links them into
:doc:`Where an answer comes from <answer-sources>` — which is the same statement
read the other way round, one heading per source with the tools it answers. What
it settles is not what a tool is about but whether it can be asked at all in the
state the machine is in.

* :doc:`typo3_server_scope <typo3_server_scope>` — Orientation for this server.
* :doc:`typo3_rule_lookup <typo3_rule_lookup>` — Search the TYPO3 rules and
  procedures this server carries, by topic.
* :doc:`typo3_script_lookup <typo3_script_lookup>` — Find notes for TYPO3 core
  scripts and commands.
* :doc:`typo3_task_guide <typo3_task_guide>` — Build a task checklist enriched
  with matching hints and relevant core checks.
* :doc:`typo3_test_run_guide <typo3_test_run_guide>` — Say what this core
  checkout needs before a test can run at all, and which
  Build/Scripts/runTests.sh commands to run once it can.
* :doc:`typo3_hint_lookup <typo3_hint_lookup>` — Return hints for TYPO3 core
  paths or task topics, grouped by section.
* :doc:`typo3_documentation_lookup <typo3_documentation_lookup>` — Search or
  read the official live TYPO3 documentation for a covered TYPO3 line.
* :doc:`typo3_forge_lookup <typo3_forge_lookup>` — Read the TYPO3 issue tracker
  at forge.typo3.org before writing a patch.
* :doc:`typo3_gerrit_lookup <typo3_gerrit_lookup>` — Find out whether a TYPO3
  core patch already exists, from the review server at review.typo3.org.
* :doc:`typo3_component_lookup <typo3_component_lookup>` — Look up TYPO3 backend
  UI components by name or topic.
* :doc:`typo3_system_extension_lookup <typo3_system_extension_lookup>` — Answer
  whether an extension is part of the TYPO3 core, and on which versions.
* :doc:`typo3_reference_list <typo3_reference_list>` — List the worked examples
  the TYPO3 core ships of its own conventions, and what each one is a reference
  for.
* :doc:`typo3_translation_domain_lookup <typo3_translation_domain_lookup>` —
  Compute the translation domain an XLF file resolves to, from its path.
* :doc:`typo3_label_lookup <typo3_label_lookup>` — Search the labels registered
  in the TYPO3 installation you are working in.
* :doc:`typo3_fluid_namespace_list <typo3_fluid_namespace_list>` — List the
  Fluid ViewHelper namespaces that are globally available in the TYPO3
  installation you are working in, so a template knows which prefixes it may use
  without declaring them.
* :doc:`typo3_configuration_lookup <typo3_configuration_lookup>` — Read an
  effective TYPO3_CONF_VARS value from the installation you are working in.
* :doc:`typo3_schema_lookup <typo3_schema_lookup>` — List the columns TYPO3
  derives for a table from its TCA.
* :doc:`typo3_backend_module_lookup <typo3_backend_module_lookup>` — List the
  backend modules registered in the TYPO3 installation you are working in, with
  the extension that declares each one, its place in the module tree, its
  labels, its access level, the route each one answers on and every sub-route it
  registers.
* :doc:`typo3_icon_lookup <typo3_icon_lookup>` — Validate or find icon
  identifiers in the TYPO3 backend icon registry of the installation you are
  working in.
* :doc:`typo3_changelog_lookup <typo3_changelog_lookup>` — Search the TYPO3
  changelog.
* :doc:`typo3_project_describe <typo3_project_describe>` — Describe the project
  around the TYPO3 installation this server was started in.
* :doc:`typo3_extension_describe <typo3_extension_describe>` — Describe what one
  installed extension registers.
* :doc:`typo3_catalog_scope <typo3_catalog_scope>` — Report whether component
  contracts come from the active installation or the bundled fallback, which
  TYPO3 core revision the fallback catalogs were taken from, what they cover,
  and how to re-check them.
* :doc:`typo3_commit_message_guide <typo3_commit_message_guide>` — Draft and
  check a TYPO3 commit message.
* :doc:`typo3_feedback_record <typo3_feedback_record>` — Leave feedback about a
  gap, wrong answer, or missing capability of this knowledge server.
* :doc:`typo3_feedback_list <typo3_feedback_list>` — List improvement feedback
  recorded via typo3_feedback_record, newest first, so they can be worked off.

.. toctree::
    :hidden:

    typo3_server_scope
    typo3_rule_lookup
    typo3_script_lookup
    typo3_task_guide
    typo3_test_run_guide
    typo3_hint_lookup
    typo3_documentation_lookup
    typo3_forge_lookup
    typo3_gerrit_lookup
    typo3_component_lookup
    typo3_system_extension_lookup
    typo3_reference_list
    typo3_translation_domain_lookup
    typo3_label_lookup
    typo3_fluid_namespace_list
    typo3_configuration_lookup
    typo3_schema_lookup
    typo3_backend_module_lookup
    typo3_icon_lookup
    typo3_changelog_lookup
    typo3_project_describe
    typo3_extension_describe
    typo3_catalog_scope
    typo3_commit_message_guide
    typo3_feedback_record
    typo3_feedback_list
    answer-sources
