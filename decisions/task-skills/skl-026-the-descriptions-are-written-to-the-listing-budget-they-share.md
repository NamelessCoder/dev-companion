---
id: D-SKL-026
title: The descriptions are written to the listing budget they share
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn
---

# D-SKL-026 — The descriptions are written to the listing budget they share

**Every published description is written to the budget the whole listing shares,
because a client that runs out of it drops a description whole rather than
shortening it.**

The description is the only part of a skill read before it is chosen
(`R-SKL-010`), and it is not paid for by its own skill. A client reads all of
them in one attachment against one character budget, so every sentence one skill
spends is taken off the skill that gets listed by its name alone.

## Evidence

- The arithmetic, read off the client this repository is worked in — the native
  binary of Claude Code 2.1.226, functions `x4t` and `zNs`. The budget is
  `floor(context window in tokens × bytes per token × skillListingBudgetFraction)`
  **characters**, the fraction defaulting to 0.01 and the bytes to 4 for the 3.x
  and 4.x models the binary lists by name and 3 for everything else. A 200k
  session is therefore 8000 characters on a 4.5-generation model and 6000 on a
  5-generation one, and a 1M session is 30000. Both defaults are settings —
  `skillListingBudgetFraction`, `skillListingMaxDescChars` — and
  `SLASH_COMMAND_TOOL_CHAR_BUDGET` overrides the result outright.
- What an entry costs and what overflow does. An entry is
  `- <name>: <description>` with the description capped at 1536 characters, plus
  one newline between entries. Over budget, the client's own bundled skills keep
  their full entry; every other skill is offered as `- <name>` and buys its
  description back for its length plus two, in the order of
  `usageCount × 0.5^(days since last use / 7)` with a floor of 0.1. That score
  is zero for every skill nobody has used, so on a fresh install the twelve are
  tied and the sort is stable — what decides which of them keeps a description
  is the order the client discovered them in.
- What the twelve cost before this change, measured on 2026-08-08: 7153
  characters of description, 7520 of entries, 7177 to buy every description
  back.
- What the client spends before them. The bundled listing measured in one
  session that day — Claude Code 2.1.226, artifacts enabled — is 5997 characters
  over 15 skills, of which `dataviz` and the four `artifact-*` skills are 1832.
  Those entries are protected, so they come off the top.
- The four combinations, with the twelve as they were. At 6000 against the full
  bundled listing the floor is 6366 with the twelve listed by name alone, so
  none of them can carry a description at any length. At 8000 against it, 1634
  characters are left and three of the twelve were described. At 6000 against
  the same client without the artifact skills, 1470. Only a 1M session listed
  all twelve.

## Decided

- The twelve are trimmed to the sides they own and the words a user brings, and
  what summarised the workflow is gone: 7153 characters of description become
  3110, 7520 of entries become 3477, and buying every description back costs
  3134 rather than 7177. Six of the twelve are described where three were, at
  8000 against the measured client; all twelve are, at 8000 against one whose
  bundled listing is nearer 4000. Which sides each description names is
  unchanged — `R-SKL-010` is what the trim was read against, not what it was
  traded for.
- The total is held by a test rather than stated in prose, because a number in a
  sentence is true on the day it is written — `R-SKL-021` and
  `SkillTest::everyDescriptionIsWrittenToALengthOfItsOwn`.
- Trimming further was rejected. What is left is the sides and the triggers, and
  the combination that still does not fit — 6000 against a bundled listing of
  5997 — does not fit at any length, because the floor is over the budget with
  the twelve carrying no descriptions at all.
- How many skills this server publishes is **not** decided here. Twelve fit a 1M
  session whole and cannot all be described in a 200k one on the current model
  generation, whatever they say, so the question is which of them a session on
  200k should be able to see. It needs a reading of which skills actually get
  activated, which nothing here has.

## Assumed

- That the bundled listing is roughly this size. It was measured in one client,
  on one machine, on one day, and which bundled skills are listed at all is
  gated by feature flags this repository cannot see.
- That other clients divide their listing the same way. This is Claude Code's
  arithmetic; the installer writes these skills into other agent clients too,
  and none of theirs was read.
- That a dropped description is what keeps a skill out of a task. `D-AUD-003`
  measured a session whose skill listing arrived in full and which still worked
  from the checkout, so the listing is one channel and not the only one.

## Wrong if

- A recorded run reaches a session where the twelve arrived described and the
  work was still done without them. Then the budget was never the obstacle for
  that task, and what is left to suspect is the wording rather than its length.
- A client whose bundled listing is well under the budget still drops one of the
  twelve. Then the arithmetic above is read wrong, and the entry cost is the
  first thing to re-measure.
- A description trimmed here stops matching a task it used to carry — a feedback
  or a run naming a request that reached nothing. Then a side or a trigger went
  out with the summary, and it goes back at the cost of another skill's.

## Since then

The third **Wrong if** happened on one skill: the trim took out the requests a
user's own words reach and put back the step clause that had been cut the same
day, and a session then read the result as a branch-switching workflow. So the
trade this entry says it did not make was made once — the sides survived the
sweep and not the trim. Putting them back was affordable and was done the same
day, which says the ratchet holds a total and not a wording: what a trim leaves
behind is read against `R-SKL-010` and `D-SKL-024` by whoever writes it.

The first **Wrong if** fired on a transcript rather than a recorded run: a
session received every description in full, byte-identical to this checkout's
and opening on the request's own two words, and worked from the checkout for
thirteen calls. So neither the budget nor the wording was the obstacle
(`D-SKL-033`). The ceiling moved on 2026-08-19, because a thirteenth skill does
not fit a ratchet set to what twelve cost at any wording.
