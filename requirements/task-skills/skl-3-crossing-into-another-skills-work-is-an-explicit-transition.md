---
id: R-SKL-3
status: held
restsOn: [D-EVI-2]
---

# R-SKL-3 — Crossing into another skill's work is an explicit transition

**A task skill crossing into work another skill owns performs an explicit
transition.**

It names the verified stopping point, stops before editing the new owner's
files, activates that owner, and carries forward only the scope and verified
behavior the next workflow needs.

Backend-module documentation is owned by the extension that contains the
functionality, not by the project around it.

## From

`EXT-04`, where the backend-module skill remained active while editing the
project README and never activated the documentation skill (2026-07-30).

## Held by

- `SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition`,
  `SKILL-07`; that a session actually performs the transition is not guarded,
  and will not be — see `D-EVI-2`.
