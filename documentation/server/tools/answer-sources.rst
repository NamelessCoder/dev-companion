.. _answer-sources:

Where an answer comes from
==========================

Every tool declares which sources can answer it, and says so at the foot of its
own description and on its page here. What that answers is not what a tool is
about but whether it can be asked at all right now: with nothing running, the
tools under knowledge and packages are the ones still worth calling. Which
source answered one call is ``answeredBy`` in that answer, where the tool has
two. This page is written by ``bin/cli tools:index`` from the Source enum.

.. image:: ../../images/answer-sources.svg
    :alt: The five sources plotted against how much of the machine has to be running:
          bundled knowledge and this server's own checkout answer with nothing
          running, packages need files on disk, the installation source needs a
          booted installation, and network sources need outbound reach.

.. _answer-sources-installation:

installation
------------

The installation this server was started in, booted or asked through its
console: its assembled state after every extension has had its say, and nothing
at all where it cannot be reached.

:doc:`typo3_server_scope <typo3_server_scope>`,
:doc:`typo3_label_lookup <typo3_label_lookup>`,
:doc:`typo3_fluid_namespace_list <typo3_fluid_namespace_list>`,
:doc:`typo3_configuration_lookup <typo3_configuration_lookup>`,
:doc:`typo3_schema_lookup <typo3_schema_lookup>`,
:doc:`typo3_backend_module_lookup <typo3_backend_module_lookup>`,
:doc:`typo3_icon_lookup <typo3_icon_lookup>`,
:doc:`typo3_extension_describe <typo3_extension_describe>`.

.. _answer-sources-packages:

packages
--------

The files the installed packages ship, read rather than executed. Answers on a
fresh clone and with the containers down; what a package registers by running is
not in it.

:doc:`typo3_component_lookup <typo3_component_lookup>`,
:doc:`typo3_label_lookup <typo3_label_lookup>`,
:doc:`typo3_fluid_namespace_list <typo3_fluid_namespace_list>`,
:doc:`typo3_icon_lookup <typo3_icon_lookup>`,
:doc:`typo3_changelog_lookup <typo3_changelog_lookup>`,
:doc:`typo3_project_describe <typo3_project_describe>`,
:doc:`typo3_extension_describe <typo3_extension_describe>`,
:doc:`typo3_catalog_scope <typo3_catalog_scope>`.

.. _answer-sources-knowledge:

knowledge
---------

The knowledge base inside this package. Needs nothing running, and is bound to
TYPO3 versions rather than to an installation.

:doc:`typo3_server_scope <typo3_server_scope>`,
:doc:`typo3_rule_lookup <typo3_rule_lookup>`,
:doc:`typo3_script_lookup <typo3_script_lookup>`,
:doc:`typo3_task_guide <typo3_task_guide>`,
:doc:`typo3_test_run_guide <typo3_test_run_guide>`,
:doc:`typo3_hint_lookup <typo3_hint_lookup>`,
:doc:`typo3_component_lookup <typo3_component_lookup>`,
:doc:`typo3_system_extension_lookup <typo3_system_extension_lookup>`,
:doc:`typo3_reference_list <typo3_reference_list>`,
:doc:`typo3_translation_domain_lookup <typo3_translation_domain_lookup>`,
:doc:`typo3_catalog_scope <typo3_catalog_scope>`,
:doc:`typo3_commit_message_guide <typo3_commit_message_guide>`.

.. _answer-sources-network:

network
-------

A service outside this machine. An unreachable one is said out loud rather than
answered as empty.

:doc:`typo3_documentation_lookup <typo3_documentation_lookup>`,
:doc:`typo3_forge_lookup <typo3_forge_lookup>`,
:doc:`typo3_gerrit_lookup <typo3_gerrit_lookup>`,
:doc:`typo3_changelog_lookup <typo3_changelog_lookup>`.

.. _answer-sources-checkout:

checkout
--------

This server's own checkout, which is why the tool offering it exists only in a
standalone one.

:doc:`typo3_feedback_record <typo3_feedback_record>`,
:doc:`typo3_feedback_list <typo3_feedback_list>`.
