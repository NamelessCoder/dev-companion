# How the work is done

One page per procedure that is long enough to get wrong from memory. One thing
is no procedure and is written by nobody: `clients/tools.md` and the pages under
`clients/tools/` are the tool surface, rendered from the classes that declare
it, each page carrying what that tool answered once.

`requirements/` and `decisions/` are working directories, and the whole of what
one of their entries is — what it states, where it goes, how it is written —
is here. Both readmes are what a command writes into them and a line pointing
back. The other directories still say what a thing **is** on their own and link
here for how it is carried out.

The split is deliberate. A readme that also holds the workflow is read by
someone looking for one paragraph and finding six; a workflow with no home ends
up in an agent's private memory, where nobody else can read it and no checkout
carries it. Both happened here before this directory existed.

Pages are grouped by the subject they belong to, because a subject outgrows one
page and then has nowhere to put the second. Four sit at the top because they
belong to no subject: this map, the words, the commands everything is kept in
order by, and the launch two of the subjects below share.

- [glossary.md](glossary.md) — what everything here is called, one line each.
- [working-on-the-server.md](working-on-the-server.md) — the commands this
  repository is kept in order by, the core checkouts, the test suite.
- [driving-a-session.md](driving-a-session.md) — what has to be true of an agent
  session started from a command line, whether it is carrying a forward run or
  working a todo in a worktree.

## [feedback/](feedback/readme.md) — how this repository works on itself

- [readme.md](feedback/readme.md) — how the work moves between `feedback/`,
  `requirements/`, `decisions/` and `todo/`, and the debrief that gets feedback
  out of a session this repository cannot read.
- [judging.md](feedback/judging.md) — what is asked of one open feedback, in
  which order and on what evidence, and which answers may be given without
  asking first.
- [working-a-todo.md](feedback/working-a-todo.md) — what is read before the todo
  `bin/cli todo:next` handed over is changed, where a question the step turns on
  is settled rather than recalled, and what the queue says afterwards.
- [working-todos-in-parallel.md](feedback/working-todos-in-parallel.md) — how
  several sessions get different todos, where each of them writes, and how what
  they wrote comes back.

## [requirements/](requirements/writing-a-requirement.md) — what has to keep holding

- [writing-a-requirement.md](requirements/writing-a-requirement.md) — the
  sections an entry is written in, what **Held by** owes a reader, and what
  `open`, `not guarded` and `held` mean.

## [decisions/](decisions/writing-a-decision.md) — what a change rested on

- [writing-a-decision.md](decisions/writing-a-decision.md) — the sections an
  entry is written in, what a later session adds to the foot of one, and what
  `open`, `confirmed` and `revoked` promise a reader.

## [knowledge/](knowledge/versions.md) — what the server answers, and from where

- [versions.md](knowledge/versions.md) — writing a statement that holds for some
  TYPO3 versions and not others.
- [asking-the-installation.md](knowledge/asking-the-installation.md) — the order
  an installation's own answers are looked up in, and what a fallback owes the
  caller.

## [clients/](clients/installing.md) — what goes into somebody else's project

- [installing.md](clients/installing.md) — every client and layout the installer
  supports, and the configuration each one gets.
- [tools.md](clients/tools.md) — every tool, and the page saying what it takes
  and which fields it answers with. Written from the registry by
  `bin/cli tools:index`, down to each page's `## Answered` heading. Below that
  heading is what the tool answered on one day, recorded by
  `bin/cli tools:record` against a core checkout and, for the tools whose answer
  the installation decides, against one this repository writes — evidence rather
  than a derivation, so nothing checks it. The two tools that are not recorded
  say why in its place.
- [writing-a-skill.md](clients/writing-a-skill.md) — what a new task skill has
  to show before it exists, the rules it is written under, and what holds each
  one.

## [evidence/](evidence/forward-runs.md) — how the server is measured

- [forward-runs.md](evidence/forward-runs.md) — running a forward review,
  judging it, and what to do when one stops without an error.

What does **not** belong here are the entries themselves: what must hold is
[requirements/](../requirements/readme.md), what a change assumed is
[decisions/](../decisions/readme.md), and the order of the work is
[todo/](../todo/readme.md), which is also where this machine's checkouts are
named. The conventions every session works under stay in
[AGENTS.md](../AGENTS.md), because they are read before anything else is.
