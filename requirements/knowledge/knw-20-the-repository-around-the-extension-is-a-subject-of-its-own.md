---
id: R-KNW-20
status: held
---

# R-KNW-20 — The repository around the extension is a subject of its own

**The repository around the extension is a subject of its own.**

The catalog is organised by subsystem, which is the model of someone who
already knows where their file goes; a project developer asks "where does this
go", and the answer for what is not part of any package — the build tooling,
the suites that need a running site, the scripts, what is ignored — is
nowhere in the core, because the core is not a project. It is answered as named
places with the reason each one exists, not as a skeleton to copy: projects
differ in whether they have Node, DDEV or one site or twenty, and only the
reasons transfer.

## From

A session that had to invent the location of the phpunit configurations, the
browser suite and its config, the scripts a project exposes, and what is
ignored — with a working answer for the extension (`sitepackage-layout`) and
none for what sits around it (2026-07-29).

## Held by

- `HintsTest::whereSomethingGoesInTheRepositoryIsAnsweredToo`
