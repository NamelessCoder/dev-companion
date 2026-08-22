---
id: D-GUI-016
title: The brief carries its hints whether or not it names a skill
date: 2026-08-19
status: open
coveredBy:
  - HintsTest::theSkillABriefNamesTakesNoHintOutOfIt
---

# D-GUI-016 — The brief carries its hints whether or not it names a skill

**A brief carries the same hints whether or not it names an owning skill, and
the skill's hint step is discharged by what the brief says about them.**

In the one measured run where a brief named a skill, the session worked from the
hints and never loaded it, which reads as the block having stood in for the
route.

## Evidence

- **What the brief actually carried, measured in this checkout on 2026-08-19.**
  "Register a new content element with a custom backend preview and a Fluid
  template", with a `tt_content` override and a content-element template under a
  site package, names `typo3-content-element-development` and carries
  `content-elements`, `fluid-templates`, `extbase-plugin-registration` and
  `sitepackage-templates`. `omittedHints` names `tca-formengine`,
  `content-element-preview`, `sitepackage-layout`, `sitepackage-initial-content`
  and `frontend-records`, and `typo3_hint_lookup` at its ceiling for the same
  paths answers those nine in that order. So the brief carries the strongest
  four of the block rather than the block, which is what `D-GUI-007` decided and
  what the reading behind this entry assumed away.
- **What the skill's own step does with it.** Step 4 of
  [`skills/base.md`](../../skills/base.md) reads the brief's own sentence:
  `HINTS_COMPLETE` says the call is made and asking again returns the same
  hints, `HINTS_TRUNCATED` beside `HINTS_OMITTED` says what is still owed and
  names it by id. The hints in a brief therefore take a query out of the skill's
  order and leave an id fetch, and withholding them puts the query back.
- **The session that loads no skill is the ordinary case.**
  [`D-SKL-033`](../task-skills/skl-033-activation-is-the-clients-and-the-order-after-it-is-what-this-server-holds.md)
  records a benchmark of eighty-two runs across four sweeps with `skills_used`
  empty on every row, one `typo3_task_guide` call among seventeen tasks. A brief
  that withheld on a skill name would withhold from the sessions that are
  actually there.
- **The name is not knowledge that the skill is installed.**
  `TaskIntents::skills()` reads `knowledge/task-intents.json`, and the `skills`
  field of the output schema says a name there is not a promise it is installed.
  What a session without it does is on the record: `feedback/2026-08-01-003356`
  built a content element with a custom backend preview and guessed at facts
  that skill's own description covers.
- **The general form is already decided.**
  [`D-SKL-034`](../task-skills/skl-034-a-step-of-the-order-is-skippable-on-what-the-session-holds-never-on-how-it-arrived.md)
  took the condition off step 3 because a step is skippable on what the session
  holds and never on how it arrived. Withholding hints on a routed name is that
  condition again, moved from the skill into the answer, where the session
  cannot even see it being applied.
- **`D-SKL-018`'s reasoning does not reach the hint block.** It names one guide
  where three competed in a sentence, so it adds a pointer and removes no
  content; the block stands on `D-GUI-007`, which is about carrying a quoted
  selection and saying whose it is. The todo made the amendment conditional on
  those being the same reasoning, and they are not.

## Decided

- **The brief is unchanged**, and this entry is the record of the question being
  asked rather than a change. `D-SKL-018` keeps its statement and gains a
  pointer here.
- **The property is guarded**, so a later change cannot gate the block on
  `skills` without the suite saying so.
- **The measurement the todo asked for is not run.** It wanted the same prompt
  through both answers, and the arm it would have to build — a server that
  withholds hints where it names a skill — is the thing this entry rejects on
  the evidence above; running it means publishing that answer to a driven
  session first.
- **A recorded run was the wrong place for it either way.** `scenarios/runs/`
  holds one run per open forward review, judged against that review's criteria,
  and `bin/cli scenarios:record` takes a review id. A prompt run twice through
  two builds of this server is neither a forward review nor a contract case, so
  nothing there could have carried it.
- **Rejected: withholding the hints where a skill is named.** It costs the
  routed session a call it does not have to make today, costs the unrouted
  session the answer entirely, and buys an activation that eighty-two runs say
  does not follow from the room being made.

## Assumed

- That a session handed no hints reaches `typo3_hint_lookup` or works without
  them, rather than loading the skill. That is the benchmark read as a prior
  about this client, and no run has been made with the block withheld.
- That what the overlap costs is tokens rather than calls, and that a session is
  charged per call (`D-FBK-020`). Four quoted hints are what that trade is
  weighed on; a brief carrying a whole workflow would be a different one.

## Wrong if

- A session that loaded the skill and read the brief calls `typo3_hint_lookup`
  with the same paths anyway. Then the sentence discharges nothing, what is
  duplicated is the call rather than the text, and the discharge is what to fix.
- A run with the block withheld loads the skill. Then the hints were competing
  with the route after all, and the trade this entry prices as one-sided is a
  real one.
- The brief grows to carry what the skill's other steps fetch — the deprecation
  sweep's entries, the component catalog — and a session stops making those
  calls on the strength of one answer. Then what is decided here for the hints
  is being read as a rule about the whole brief, and where it stops is the thing
  to write down.
