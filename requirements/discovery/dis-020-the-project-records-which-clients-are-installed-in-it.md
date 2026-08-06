---
id: R-DIS-020
status: held
---

# R-DIS-020 — The project records which clients are installed in it

**Install records every client it set up in `.typo3-dev-companion/state.json`,
and an update without `--agent` refreshes all of them.**

A project is usually worked on by more than one client, and which ones is
knowledge only the project has. Naming them one at a time meant remembering a
list nobody keeps, so a second client silently kept the skills of the version it
was installed with. Clients that share a skills directory are published once.

Naming no client is a setup of its own, recorded as `generic`: the `.mcp.json`
entry and the skills at `.agents/skills`, the two locations a client finds
without being configured for it. It is recorded and refreshed like any named
client and needs no case of its own; `--agent=` does not take it, because it is
nobody's name. An update in a project where nothing is installed says so rather
than reporting work it did not do.

What the record is read for is the refresh: a skill this package has stopped
shipping is taken out of every client it reached, whichever of them the run was
told about. Where the record was kept before, and why it sits below a directory
that ignores itself now, is
[`R-DIS-024`](dis-024-the-published-directories-ignore-themselves.md).

## From

An update that had to be repeated per client, in a project set up for two of
them (2026-07-31).

## Held by

- `InstallerRecordTest`
