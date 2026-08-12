:layout: marketing

TYPO3 Dev Companion
===================

A local MCP server (plain PHP) that helps coding agents implement, review and
verify TYPO3 work for the three audiences that do it: the core contributor, the
extension author and the site developer. It establishes the project and
installation the agent is working in, supplies current, version-bound TYPO3
knowledge, and hands task-specific workflows to the skills that own them.

It answers for TYPO3 **12.4**, **13.4**, **14.3** and **main**. A statement that
does not hold on all of them names the ones it does, so an LTS is never handed a
convention that only the development line has.

.. warning::

    **Experimental.** This is a 0.x package and its surface is not settled. The
    package name, the binary, the namespace and the environment variables all
    changed in one cut on 2026-08-06. The tool names and the shapes they answer
    in can move the same way. Pin a commit where you depend on it.

.. card-grid::
    :columns: 3

    .. card:: :doc:`Install it <usage/installing>`
        :label: Usage
        :icon: actions-rocket
        :action: What the install writes

        One command writes the client entry and publishes the skills, into the
        two locations a client finds without being configured for it.

    .. card:: :doc:`Ask it something <server/index>`
        :label: Server
        :icon: actions-book
        :action: Every tool, one page each

        What it can be asked, what each tool takes, and what one call to it came
        back with.

    .. card:: :doc:`Work on it <contributing/index>`
        :label: Contributing
        :icon: actions-code-merge
        :action: The rules a session works under

        The commands this repository is kept in order by, what has to be true of
        a session doing the work, and the rules a task skill is written under.

.. band:: What a session stops searching for
    :quiet:

The coding agent keeps the checkout: which files changed, which branch it is on
and which tests cover them are read there. What the work needs around that
reading is what this server supplies, and it says which repository each answer
is for — worked out from structure rather than from wording, so a project is
never handed a rule only the core has.

.. grid::

    .. teaser:: Which convention governs this path
        :href: server/tools/typo3_rule_lookup
        :tag: Knowledge

        The rules that apply to the file being changed, bound to the TYPO3
        versions they hold for.

    .. teaser:: What the component is called
        :href: server/tools/typo3_component_lookup
        :tag: Catalog

        The backend UI a change has to be written in: the markup, the icon
        identifier and the label resource that have to be literally right.

    .. teaser:: What this installation registers
        :href: server/tools/typo3_project_describe
        :tag: Installation

        The project's own packages, and the registries no bundled answer could
        be right about.

    .. teaser:: Which deprecation lands in between
        :href: server/tools/typo3_changelog_lookup
        :tag: Changelog

        What the core changed between two versions, and what an upgrade has to
        follow.

    .. teaser:: Which check the change has to survive
        :href: server/tools/typo3_test_run_guide
        :tag: Verification

        The suite that covers the changed path, and the command that runs it
        where this project runs it.

    .. teaser:: What the commit message says
        :href: server/tools/typo3_commit_message_guide
        :tag: Workflow

        The subject, the widths and the keywords the receiving workflow expects.

.. band:: Where an answer comes from

**It answers from three sources.** Almost everything comes from the bundled
``knowledge/`` files, which are bound to versions. Broad API, reference and
tutorial questions are searched in the official live TYPO3 documentation
instead, with the requested release and canonical source on every result. And
some questions have no bundled answer that could be right — which labels exist,
which icons are registered, what a configuration value is after every extension
has had its say. Those are properties of an installation, so the server finds
the one you are working in and asks *it*, through its own console or by booting
it in a subprocess of its own.

.. image:: images/system-overview.svg
    :alt: Everything that answers a question already sits on the developer's
          machine: an MCP client starts the server as a local subprocess, which
          reads bundled knowledge and the project's own packages and
          installation. Exactly one read-only path crosses the boundary, to
          official documentation and core services.

**It answers before the installation does.** The bundled knowledge needs nothing
running, and that is the state much of it is asked in: a project that does not
exist yet, an upgrade that left the site unbootable, a core checkout with no
database. Where the installation itself is the question it is booted through its
own interpreter, in its own container where it runs in one. Where it comes up
without its essential configuration, every registry in it answers with a subset
that looks like the whole — so that state is named rather than passed on as an
answer. Where it does not come up at all, the packages are read instead, and
every answer carries which of the two it came from and what that leaves out.

The five sources an answer can come from, and what each one needs running before
it can answer, are :doc:`the tools' own table <server/tools/answer-sources>`.

.. band:: What it will not do
    :quiet:

.. grid::

    .. teaser:: It reads

        Nothing is written into the TYPO3 installation it is pointed at. The one
        exception is the feedback channel, which writes into this server's own
        checkout and is offered from a standalone checkout alone.

    .. teaser:: It starts nothing

        Nothing on your machine is started as a side effect of a lookup. A
        stopped DDEV project is reported with the command that would fix it.

    .. teaser:: It stays where you started it

        The client launches it as a subprocess over stdio, so there is no server
        to host, no network exposure and no auth to configure — the process
        boundary is the trust boundary.

    .. teaser:: It is queried in English

        The knowledge is written in English and the matching is lexical, so the
        agent translates the subject before calling and the answer back
        afterwards. The server states this at initialize.

Scope answers describe what is present without treating it as correct. The
knowledge and skills supply the conventions that apply, so code found in one
installation is not repeated as a pattern merely because it runs.

.. band:: Trained by being used

An agent gets a real task in a real checkout and works under one rule: whatever
it would otherwise search for, it asks this server first. Where the server
answers, that answer is what the task is done from. Where it does not, the agent
solves the task on its own — and that is the half worth something, because the
agent now holds an answer the knowledge base did not have. So the session ends
by handing it back, and a gap found that way arrives with its answer attached.

That is also what decides what gets built next. What this server does not answer
yet is mostly what no session has handed back yet, and a boundary is the other
thing and is stated as one in ``typo3_server_scope``. What becomes of one that
was handed back is :doc:`records/ <records/index>`.

.. band:: The manual

Which section a page belongs to is decided by who reads it. One page per
procedure that is long enough to get wrong from memory.

.. card-grid::
    :columns: 2

    .. card:: :doc:`Usage <usage/index>`
        :icon: actions-rocket

        Having this server answering in your own project: what the install
        writes into a client, and what you are agreeing to when it does.

    .. card:: :doc:`Server <server/index>`
        :icon: actions-book

        What it can be asked and where each answer comes from: every tool one
        page each, the resources beside them, the five sources, the bundled
        knowledge, the version binding, and what an installation is asked
        directly.

    .. card:: :doc:`Contributing <contributing/index>`
        :icon: actions-code-merge

        Working on the server itself: the commands this repository is kept in
        order by, what has to be true of a session doing the work, and the rules
        a task skill is written under.

    .. card:: :doc:`Records <records/index>`
        :icon: actions-list

        What is written down and where: how a feedback becomes a todo, what a
        requirement and a decision each hold, and how a forward run is measured.

Part of two sections is written by nobody. ``server/tools/`` is rendered from
the classes that declare each tool, down to the heading its recorded answer sits
under, by ``bin/cli tools:index``; ``records/`` describes four working
directories whose entries are the things themselves.

What does **not** belong here are those entries: what must hold is
`requirements/ <../requirements/readme.md>`_, what a change assumed is
`decisions/ <../decisions/readme.md>`_, and the order of the work is
`todo/ <../todo/readme.md>`_, which is also where this machine's checkouts are
named. The conventions every session works under stay in
`AGENTS.md <../AGENTS.md>`_, because they are read before anything else is.

.. toctree::
    :hidden:

    usage/index
    server/index
    contributing/index
    records/index
