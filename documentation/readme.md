# How the work is done

Four sections, and which one a page belongs to is decided by who reads it.

- **[Usage](usage/readme.md)** — having this server answering in your own
  project: what the install writes into a client, and what you are agreeing to
  when it does.
- **[Server](server/readme.md)** — what it can be asked and where each answer
  comes from: every tool one page each, the resources beside them, the five
  sources, the version binding, and what an installation is asked directly.
- **[Contributing](contributing/readme.md)** — working on the server itself: the
  commands this repository is kept in order by, what has to be true of a session
  doing the work, and the rules a task skill is written under.
- **[Records](records/readme.md)** — what is written down and where: how a
  feedback becomes a todo, what a requirement and a decision each hold, and how
  a forward run is measured.

One page per procedure that is long enough to get wrong from memory. The split
between a page here and the entry it describes is deliberate: a readme that also
holds the workflow is read by somebody looking for one paragraph and finding
six, and a workflow with no home ends up in an agent's private memory, where
nobody else can read it and no checkout carries it. Both happened here before
this directory existed.

Part of two sections is written by nobody. `server/tools/` is rendered from the
classes that declare each tool, down to the heading its recorded answer sits
under, by `bin/cli tools:index`; `records/` describes four working directories
whose entries are the things themselves.

What does **not** belong here are those entries: what must hold is
[requirements/](../requirements/readme.md), what a change assumed is
[decisions/](../decisions/readme.md), and the order of the work is
[todo/](../todo/readme.md), which is also where this machine's checkouts are
named. The conventions every session works under stay in
[AGENTS.md](../AGENTS.md), because they are read before anything else is.
