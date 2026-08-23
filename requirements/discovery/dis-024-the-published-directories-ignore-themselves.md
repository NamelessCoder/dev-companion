---
id: R-DIS-024
title: 'The published directories ignore themselves'
status: held
restsOn: [D-DIS-010]
heldBy:
  - InstallerRecordTest::neitherCommandWritesIntoTheProjectsGitignore
  - InstallerTest::gitReportsTheProjectsOwnFilesAndNothingThisPackageWrote
---

# R-DIS-024 — The published directories ignore themselves

**Every directory `install` and `update` write into a project carries a
`.gitignore` of its own saying `*`, and neither command adds a line to the
project's.**

A published skill is generated: it is replaced whole on the next run and belongs
in nobody's history. Saying so from the project's own `.gitignore` meant a
tracked, shared file gained a block that grew with every client — 32 lines in a
project set up for three of them — and that every install and update since
produced a diff in a file this package does not own.

A directory can say the same thing about itself. `*` covers everything below it
and that file with it, so git reports nothing there, while a skill the project
wrote itself, in the same skills directory, stays visible. The record moves to
`.typo3-dev-companion/state.json` for the same reason: a file at the root cannot
ignore itself, and it was the one artefact that left no other option.

Merged agent and MCP configuration — `.mcp.json`, `.codex/config.toml` and the
rest — is ignored nowhere, because the project may share it. That was already
true of those files and is now true of the `.gitignore` as well, which a project
shares more than any of them.

Nothing migrates what a development build left in a project. The package is
unreleased, so what a run of it wrote is undone by whoever ran it.

## From

The remark that installing the skills always means a change to the `.gitignore`,
2026-08-03.

## Held by

The first asks git rather than the files: it installs into a repository of its
own, next to a skill the project wrote, and holds that `git status` reports the
project's files and none of this package's. The second holds the `.gitignore` a
project brought with it against an install and an update over the top of it.
