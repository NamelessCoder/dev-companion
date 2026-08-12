# Contributing

The commands this repository is kept in order by, and what has to be true of the
session doing the work.

- [working-on-the-server.md](working-on-the-server.md) — the checkouts a
  knowledge change is verified against, the environments, the test suite, and
  how the documentation is published.
- [driving-a-session.md](driving-a-session.md) — what has to be true of an agent
  session started from a command line, whether it is carrying a forward run or
  working a todo in a worktree.
- [writing-a-skill.md](writing-a-skill.md) — what a new task skill has to show
  before it exists, the rules it is written under, and what holds each one.
- [glossary.md](glossary.md) — what everything here is called, one line each.

A skill is the one thing written here that leaves the repository and stays: it
is copied into somebody's project, where the next release of this server does
not correct it. That is why it is written under rules of its own and why every
one of them names the test that holds it.

Where the work comes from and what is left behind for the next session is
[what is written down](../records/readme.md). The conventions every session
works under are [AGENTS.md](../../AGENTS.md), because they are read before
anything else is.
