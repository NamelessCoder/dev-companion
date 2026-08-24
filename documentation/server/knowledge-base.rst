:navigation-title: Knowledge base

The bundled knowledge
=====================

Everything the tools and resources answer from ships in the package, in
`knowledge/ <../../knowledge/>`_. It needs nothing running: no installation, no
network, no database. What is read from an installation instead, and why, is
:doc:`asking the installation <asking-the-installation>`.

* ``documents/`` — the prose corpus ``typo3://guides/{documentId}`` serves and
  ``typo3_rule_lookup`` searches: ``core/contribution/rules.md``,
  ``core/testing/scripts.md``, ``core/contribution/commit-messages.md``,
  ``core/contribution/gerrit-workflow.md``, ``core/contribution/sources.md``
* ``hints/`` — one file per subject: ``datahandler.json``, ``fal.json``,
  ``labels.json``, ``site-sets.json`` and many more. Each entry names the
  domains it is asked from in its own ``domains`` field, so the file says what
  the hint is about and the tag says which query reaches it
* ``catalog/`` — the component catalog (``component/entries.json``,
  ``component-checklist.json``, ``references.json``, ``meta.json``) and the
  shipped system extensions (``system-extensions.json``)
* ``test-suite-hints.json``, ``task-intents.json``, ``icon-concepts.json``,
  ``server-scope.json``, ``versions.json``

All of it is read fresh on every request, so editing a file takes effect
immediately — no restart and no rebuild. Which TYPO3 lines a statement holds for
is data on the statement rather than a version number in its sentence:
:doc:`versions`.

Two upstream sources this corpus is written against, for a reader who wants the
original:

* TYPO3 Core Contribution Guide:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/
* TYPO3 Core Commit Message Rules:
  https://docs.typo3.org/m/typo3/guide-contributionworkflow/main/en-us/Appendix/CommitMessage.html
