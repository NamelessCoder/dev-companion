TYPO3 Dev Companion
===================

.. warning::

    **Experimental.** This is a 0.x package and its surface is not settled. The
    package name, the binary, the namespace and the environment variables all
    changed in one cut on 2026-08-06. The tool names and the shapes they answer
    in can move the same way. Pin a commit where you depend on it.

A local MCP server (plain PHP) that helps coding agents implement, review and
verify TYPO3 work for the three audiences that do it: the core contributor, the
extension author and the site developer. It establishes the project and
installation the agent is working in, supplies current, version-bound TYPO3
knowledge, and hands task-specific workflows to the skills that own them.

It answers for TYPO3 **12.4**, **13.4**, **14.3** and **main**. A statement that
does not hold on all of them names the ones it does, so an LTS is never handed a
convention that only the development line has.

Scope answers describe what is present without treating it as correct. The
knowledge and skills supply the conventions that apply, so code found in one
installation is not repeated as a pattern merely because it runs. They cover how
TYPO3's subsystems are used, the core's own contribution process, and a catalog
of backend UI components: context otherwise spread across project files, core
conventions and the official documentation.

.. image:: images/system-overview.svg
    :alt: Everything that answers a question already sits on the developer's
          machine: an MCP client starts the server as a local subprocess, which
          reads bundled knowledge and the project's own packages and
          installation. Exactly one read-only path crosses the boundary, to
          official documentation and core services.

**It answers from three sources.** Almost everything comes from the bundled
``knowledge/`` files, which are bound to versions: a statement that does not
hold on every covered TYPO3 line carries the ones it does. Broad API, reference
and tutorial questions are searched in the official live TYPO3 documentation
instead, with the requested release and canonical source on every result. And
some questions have no bundled answer that could be right — which labels exist,
which icons are registered, what a configuration value is after every extension
has had its say. Those are properties of an installation, so the server finds
the one you are working in and asks *it*, through its own console or by booting
it in a subprocess of its own.

**It answers before the installation does.** The bundled knowledge needs nothing
running, and that is the state much of it is asked in: a project that does not
exist yet, an upgrade that left the site unbootable, a core checkout with no
database. Where a question is about the installation itself, that installation
is booted through its own interpreter, in its own container where it runs in
one. Where it comes up without its essential configuration it holds core
packages only, and every registry in it answers with a subset that looks like
the whole — so that state is named rather than passed on as an answer. Where it
does not come up at all, the packages are read instead, and every answer carries
which of the two it came from and what that leaves out.

**It is queried in English**, whatever language the user is speaking. The
knowledge is written in English and the matching is lexical, so a query in
another language reaches only the technical loanwords the two share. The agent
translates the subject before calling and the answer back afterwards; the server
states this in the instructions it sends at initialize and in
``typo3_server_scope``.

**It supports the patch without writing it.** The coding agent keeps the
checkout: which files changed, which branch it is on and which tests cover them
are read there. The server supplies what the work needs around that reading:
which conventions govern a concrete path, which markup, icon identifier and XLF
resource have to be literally right, which deprecation lands in between, which
check the change has to survive, and what the commit message says.

**The conventions are the core's own**, and several of them have no counterpart
in a project or an extension — the changelog, the Gerrit workflow, the core
testing suites. So the answers say which repository they are for, worked out
from structure rather than from wording. What transfers is still answered; what
only the core has is left out with the reason. The tools are not: every client
is offered all of them, because whether a task is core work is a property of the
task and the tool list cannot vary per task.

**Those files are trained by being used.** An agent gets a real task in a real
checkout and works under one rule: whatever it would otherwise search for, it
asks this server first. Where the server answers, that answer is what the task
is done from. Where it does not, the agent solves the task on its own — and that
is the half worth something, because the agent now holds an answer the knowledge
base did not have. So the session ends by handing it back, and a gap found that
way arrives with its answer attached. That is also what decides what gets built
next: what this server does not answer yet is mostly what no session has handed
back yet, and a boundary is the other thing and is stated as one in
``typo3_server_scope``. What becomes of one that was handed back is
:doc:`records/ <records/index>`.

It is built on the official `mcp/sdk <https://packagist.org/packages/mcp/sdk>`_
and speaks **stdio** (``bin/typo3-dev-companion``): the MCP client launches it
as a subprocess, so there is no server to host, no network exposure, and no auth
to configure — the process boundary is the trust boundary. Request serving is
read-only apart from the explicit feedback tool, and setup writes only when
asked. Nothing on your machine is started as a side effect of a lookup: a
stopped DDEV project is reported with the command that would fix it.

Four sections
-------------

Which one a page belongs to is decided by who reads it.

* :doc:`Usage <usage/index>` — having this server answering in your own project:
  what the install writes into a client, and what you are agreeing to when it
  does.
* :doc:`Server <server/index>` — what it can be asked and where each answer
  comes from: every tool one page each, the resources beside them, the five
  sources, the bundled knowledge, the version binding, and what an installation
  is asked directly.
* :doc:`Contributing <contributing/index>` — working on the server itself: the
  commands this repository is kept in order by, what has to be true of a session
  doing the work, and the rules a task skill is written under.
* :doc:`Records <records/index>` — what is written down and where: how a
  feedback becomes a todo, what a requirement and a decision each hold, and how
  a forward run is measured.

One page per procedure that is long enough to get wrong from memory. The split
between a page here and the entry it describes is deliberate: a readme that also
holds the workflow is read by somebody looking for one paragraph and finding
six, and a workflow with no home ends up in an agent's private memory, where
nobody else can read it and no checkout carries it. Both happened here before
this directory existed.

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
