---
date: 2026-07-30T17:38:21+02:00
category: idea
status: closed
closed: 2026-07-31
commit: dcfc815
subject: "[TASK] State how a skill is written, once, with what holds each rule"
---

# Task-skill forward evidence is not repeatable

## Observation

**Trimmed 2026-08-01.** The runner half is done: `bin/cli scenarios` and
`scenarios/runs/` retain the prompt, environment, criteria digest, activated
skills, tool trace and one judgment with evidence per criterion, and the verdict
is derived rather than written down. Four recorded `REVIEW-01` runs took it from
`unrun` to `covered`. The reconciliation this note asked for is moot: `EXT-04`
and `SITE-07` became targeted contract cases on 2026-07-31 and are deliberately
never forward evidence.

What is still spread out is the **authoring** contract — how a skill is written,
as opposed to the order a task runs in. `skills/base.md` now holds the second:
one file, copied into every published skill, fixing project scope → extension
scope → task guide → conventions per subsystem → then the checkout. The first is
still distributed across `SkillTest` assertions and prose in each skill, though
the stable rules are visible: keep the main body procedural, keep versioned
facts in their owning tools, load one-hop references on demand, state ownership,
verification and failure boundaries, and require a realistic scenario before a
new domain becomes a skill.

## Suggestion

State the authoring contract in one small reusable form the way the evidence
order now is, and make it executable where possible — several of those rules are
already assertions in `SkillTest` and could be read from one place instead of
restated per skill.

Its last rule holds regardless and governs the three remaining `todo.md` items:
do not add skills for release, static analysis, performance, security or other
domains until a scenario or recorded session shows that the existing tools and
skills fail to carry that task.
