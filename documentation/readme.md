# How the work is done

One page per procedure that is long enough to get wrong from memory. The
directories keep saying what a thing **is** — a requirement, a decision, a
forward review, a feedback — and each links here for how it is carried out.

The split is deliberate. A readme that also holds the workflow is read by
someone looking for one paragraph and finding six; a workflow with no home ends
up in an agent's private memory, where nobody else can read it and no checkout
carries it. Both happened here before this directory existed.

Pages are grouped by the subject they belong to, because a subject outgrows one
page and then has nowhere to put the second. Three sit at the top because they
belong to no subject: this map, the words, and the commands everything is kept
in order by.

- [glossary.md](glossary.md) — what everything here is called, one line each.
- [working-on-the-server.md](working-on-the-server.md) — the commands this
  repository is kept in order by, the core checkouts, the test suite.

## [feedback/](feedback/readme.md) — how this repository works on itself

- [readme.md](feedback/readme.md) — how the work moves between `feedback/`,
  `requirements/`, `decisions/` and `todo/`, and the debrief that gets
  feedback out of a session this repository cannot read.
- [judging.md](feedback/judging.md) — what is asked of one open feedback, in
  which order and on what evidence, and which answers may be given without
  asking first.
- [working-a-todo.md](feedback/working-a-todo.md) — what is read before the todo
  `bin/cli todo:next` handed over is changed, where a question the step turns on is
  settled rather than recalled, and what the queue says afterwards.

## [knowledge/](knowledge/versions.md) — what the server answers, and from where

- [versions.md](knowledge/versions.md) — writing a statement that holds for some
  TYPO3 versions and not others.
- [asking-the-installation.md](knowledge/asking-the-installation.md) — the order
  an installation's own answers are looked up in, and what a fallback owes the
  caller.

## [clients/](clients/installing.md) — what goes into somebody else's project

- [installing.md](clients/installing.md) — every client and layout the installer
  supports, and the configuration each one gets.
- [writing-a-skill.md](clients/writing-a-skill.md) — what a new task skill has
  to show before it exists, the rules it is written under, and what holds each
  one.

## [evidence/](evidence/forward-runs.md) — how the server is measured

- [forward-runs.md](evidence/forward-runs.md) — running a forward review,
  judging it, and what to do when one stops without an error.

What does **not** belong here: what must hold — that is
[requirements/](../requirements/readme.md); what a change assumed —
[decisions/](../decisions/readme.md); the order of the work —
[todo/](../todo/readme.md), which is also where this machine's checkouts are named;
and the conventions every session works under, which stay in
[AGENTS.md](../AGENTS.md) because they are read before anything else is.
