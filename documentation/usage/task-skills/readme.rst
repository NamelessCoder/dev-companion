:navigation-title: Task skills

The installed task skills
=========================

The installer publishes task workflows beside the server configuration. A client
chooses one from its description, loads it for the task, and follows the order
it defines while the MCP tools supply the project and TYPO3 facts that order
needs.

This is the ordinary set written by ``install`` and refreshed by ``update``. The
path depends on the client and is listed in :doc:`../installing`; the generic
install writes it below ``.agents/skills``. Each installed directory also gets
``references/base.md``, the shared start of every workflow, so copying the
source directory by hand does not produce the same skill.

Only published skills are listed. A skill whose front matter declares it a draft
is installed only when the command takes ``--drafts``, and the next ``update``
without that flag removes it again. :doc:`../checking-it-answers` explains how
to tell a stale copy from a workflow the client did not activate.

Published workflows
-------------------

Each name opens one page containing the unchanged Markdown workflow and every
reference it hands to the agent. The description after it is the skill's own
selection description.

.. The list and its pages are written into the published copy by
   ``bin/cli documentation:prepare``.
