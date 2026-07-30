---
date: 2026-07-30T17:38:21+02:00
category: idea
status: open
---

# Task-skill forward evidence is not repeatable

## Observation

The task skills have realistic prompts and static contract tests, but no
repeatable runner records whether an agent activates the applicable skill,
follows its evidence order and satisfies the scenario's positive and negative
criteria. `SkillTest` verifies text, routing order and the presence of scenario
sections; the behavioral forward runs remain manual. Their recorded status can
therefore drift from the current implementation: `EXT-04` is still marked
partial while the closely related `SITE-07` is covered by the backend-module
skill.

The authoring contract is also distributed across existing skills, tests and
requirements rather than stated in one small reusable form. The stable rules
are already visible: keep the main body procedural, keep versioned facts in
their owning tools, load one-hop references on demand, state ownership,
verification and failure boundaries, and require a realistic scenario before a
new domain becomes a skill.

## Suggestion

First re-run `EXT-04` and `SITE-07` against the current server and reconcile
their status. Then give the forward scenarios a machine-readable result format
and the smallest runner that can retain the prompt, environment, required
outcomes, failure conditions, tool trace and verdict without duplicating the
human-readable scenario. Make the authoring contract executable where possible.
Do not add skills for release, static analysis, performance, security or other
domains until a scenario or recorded session shows that the existing tools and
skills fail to carry that task.
