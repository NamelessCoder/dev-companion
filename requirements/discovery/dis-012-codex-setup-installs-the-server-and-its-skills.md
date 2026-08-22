---
id: R-DIS-012
title: 'Codex setup installs the server and its skills'
status: held
---

# R-DIS-012 — Codex setup installs the server and its skills

**Codex setup installs both the MCP entry and the task skills through an
explicit agent option.**

An update replaces its complete generated skill directories while preserving
unrelated skills and configuration; a conflicting server entry is reported
rather than replaced. Repeated install and update calls are idempotent. The
central generated state and only the package-owned skill directories are added
to `.gitignore`; merged MCP and agent configuration remains versionable. In a
DDEV project the generated client entry runs the Composer binary through DDEV,
while the skills are published into the host-mounted project.

## From

`META-05`.

## Held by

- `InstallerTest`
