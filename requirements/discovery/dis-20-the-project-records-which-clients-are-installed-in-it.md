---
id: R-DIS-20
status: held
---

# R-DIS-20 — The project records which clients are installed in it

**Install records every client it set up in `typo3-cms-mcp.json`, and an update
without `--agent` refreshes all of them.**

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

The `.gitignore` entries follow from that record and are written whole between
`# BEGIN typo3-cms-mcp` and `# END typo3-cms-mcp`: a client that is gone or a
skill that was renamed leaves no line behind, and nothing outside the markers is
touched.

## From

An update that had to be repeated per client, in a project set up for two of
them (2026-07-31).

## Held by

- `InstallerRecordTest`
