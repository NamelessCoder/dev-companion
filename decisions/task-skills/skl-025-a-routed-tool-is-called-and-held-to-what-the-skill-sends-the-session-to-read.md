---
id: D-SKL-025
date: 2026-08-08
status: open
---

# D-SKL-025 — A routed tool is called and held to what the skill sends the session to read

**Each of the four calls `skills/base.md` fixes is made, and the keys the base
sends the session to read are asserted on the answer that comes back.**

Every other rule of the authoring contract is read off the file, which
[writing-a-skill.md](../../documentation/contributing/writing-a-skill.rst)
states itself: the wording is present, and a reorganisation can leave it present
while the behaviour goes.

## Evidence

- A skill does not only name a tool, it says what to read out of the answer.
  Read off `skills/base.md` on 2026-08-08: step 1 has the session read whether a
  declared command is a check, a change or unknown, and the guides it ends with
  as ids; step 2 the test layers and the source language each XLF declares; step
  3's condition on step 4 is a sentence in the brief rather than the `hints`
  key, which is populated either way.
- `ROUTING_SKILLS` in `SkillTest` records which tools each skill routes through
  and in what order, and nothing calls one of them. So a tool that stops
  reporting one of those keys fails nothing: the skill still names it, the
  routing assertion still passes, and the session is sent to a key that is not
  there.
- Mutated on 2026-08-08 against green assertions, one at a time: dropping
  `guides` from the project answer, renaming the `change` marking to `rewrite`,
  and rewording the sentence a brief that carried every matched hint prints.
  Each turns the new assertion red and leaves every other skill assertion green.
- `WordPress/agent-skills` is where the mechanism comes from: its
  `eval/harness/run.mjs` runs what a skill routes to and checks the shape it
  depends on. The rest of that harness is not worth copying — it validates front
  matter in 133 lines, its 41 scenario files have no consumer in the repository,
  and its own `docs/ai-authorship.md` says no formal evaluation system exists.

## Decided

- The four the base fixes come first, because every skill inherits them and
  their drift therefore costs the most. The tools in `ROUTING_SKILLS` are not
  covered here.
- The calls are threaded rather than listed: the extension key comes out of step
  1's answer and the hint id out of step 3's, the way a session reads them. What
  is held is the pair, not two shapes that happen to be asserted in one method.
- What is asserted is the key the base sends the session to, never the value
  this installation happens to give it. A value pinned here makes every change
  to the fixture a change to `SkillTest` as well.
- The two calls that need an installation are asked of the one `Upkeep\Fixture`
  writes. A core checkout has no console to answer them and a real site is one
  machine's, so neither could carry an assertion that runs everywhere. That is
  the exception `R-COD-003` already stood on for `FixtureTest`, and it is named
  there now rather than left to a second reader to rediscover.
- It is not a second `ToolContractTest`. That holds every answer to the schema
  its own tool declares, and a tool's schema can be right while the base sends
  the session to a key no schema requires.

## Assumed

- The base's four are where drift costs most. Nothing measured that against the
  `ROUTING_SKILLS` tools; no recorded session has lost a step to one of those
  changing shape yet.
- The written installation answers these four the way a real one does. That is
  what `FixtureTest` already rests on, and this adds no new claim to it.

## Wrong if

- A skill's prose goes stale against a tool that kept every key — the shape held
  and the sentence around it stopped being true, which this cannot see and which
  would say the gap was narrower than the fix.
- The fixture stops producing one of the branches the assertion reads — a brief
  that stops short, an extension of the project's own — and the test is kept
  green by loosening it rather than by shaping the fixture.
- A tool that only `ROUTING_SKILLS` names drifts and costs a session a step,
  which would say the four were the wrong place to stop.

## Covered by

- `SkillTest::everyCallTheBaseFixesAnswersWithWhatItSendsTheSessionToRead`
