---
id: D-AUD-004
date: 2026-08-02
status: open
---

# D-AUD-004 — Every client is offered every tool, and the answer says who it obliges

**Every client is offered every tool; what an answer is worth outside the core
is said in the answer, and only the caller shortens the tool list.**

The list is shortened by naming tools in `TYPO3_DEV_COMPANION_EXCLUDE_TOOLS`,
and by nothing else.

[`D-AUD-002`](aud-002-two-profiles-because-a-third-would-have-been-the-same-set.md)
withheld three tools from a Composer project, on the grounds that a repository
with no `Build/Scripts/`, no Gerrit remote and no Forge issue cannot follow the
core's contribution process. Its own **Corrected on** line of 2026-08-02 showed
what that cost, and this entry is what came of asking whether the mechanism was
the right one at all.

## Evidence

- The tool list a client pays for here is 92,189 bytes across 23 tools. The
  three the `project` profile removed — `typo3_rule_lookup`,
  `typo3_script_lookup`, `typo3_test_run_guide` — are 2,223 of them, 2.4%. The
  profile was reasoned about as a cost decision and was not one.
- The knowledge was never withheld. `D-AUD-002` decided the resources are not
  filtered, so every core document stayed readable as a `typo3://core` resource
  under the `project` profile. What the profile removed was the doorway, and
  Scope::offered() then edited the map so the doorway was not mentioned —
  leaving a client carrying knowledge it could no longer find.
- Ten recorded sessions in `E-SITE` called `typo3_task_guide` eight times and
  not one was core-shaped, so the derivation was right every time it ran and
  gained nothing on any of them. The one shape it got wrong is the one that
  broke: a core patch answered as core work, routed to `typo3_test_run_guide`
  twice, and the client could not call it.

## Decided

- `Server\Profile` is deleted rather than reduced to an explicit preset.
  `TYPO3_DEV_COMPANION_PROFILE=project` was a name for one exclude list, and
  keeping it would have kept the concept that made the collision possible.
- `typo3_server_scope` cannot be excluded. It is what tells a client why the
  list is shorter than the documentation says, and a client that has lost it
  cannot tell a configured server from a broken one.
- The `provenance` of a topic no longer decides whether the topic is offered. It
  says what an answer is worth outside the core, which is R-SCO-006, and that is
  a statement about the answer rather than about the list.
- The `profile` object in the `typo3_server_scope` output schema is replaced by
  `excludedTools`, not kept alongside it. A field whose concept is gone has no
  honest value, and this is a breaking change to that schema.

## Assumed

- An agent offered a tool it cannot usefully follow does better than one offered
  nothing. `typo3_test_run_guide` in a project answers with `runTests.sh`, which
  exists in the core repository alone — the bet is that a correction beats a
  gap, and that R-SCO-001's per-path audience is what keeps the correction
  attached.
- Nobody was relying on the shorter list to keep an agent away from core-only
  advice. Nothing in `feedback/` or `scenarios/runs/` shows an agent reaching
  for one of the three tools where it did not belong, but nothing shows it being
  prevented either — the profile was never observed working.

## Wrong if

- A session in a site installation calls `typo3_test_run_guide` or
  `typo3_rule_lookup` for project work and presents `runTests.sh` or the Gerrit
  review rules as if the caller could follow them. That is the failure the
  profile was aimed at, and it now has to be caught by what the answer says
  rather than by the tool being absent.
- The 2.4% turns out to be measured against the wrong thing — a client that pays
  per tool rather than per byte, or one whose limit is a count. Then the
  argument that the profile bought nothing does not hold.
