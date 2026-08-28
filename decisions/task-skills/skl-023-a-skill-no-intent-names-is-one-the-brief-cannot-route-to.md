---
id: D-SKL-023
title: 'A skill no intent names is one the brief cannot route to'
date: 2026-08-08
status: open
coveredBy:
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
---

# D-SKL-023 — A skill no intent names is one the brief cannot route to

**Three of the twelve published skills are named by no entry in
`knowledge/task-intents.json`, so `typo3_task_guide` cannot route to them and
routes somewhere else instead.**

## Evidence

- `feedback/2026-08-07-233443`. A session triaging a core bug called the guide
  as `references/base.md` step 3 requires, described the task as "Triage an old
  open core bug report", and got `skills: ["typo3-extension-conformance"]` — a
  workflow for reviewing an extension or sitepackage repository, in a checkout
  `typo3_project_describe` had reported one call earlier as `core-checkout` with
  `extensions: []`.
- Re-run on 2026-08-08 with the session's own arguments: same answer, and the
  checklist that comes with it is patch-review content — "enumerate what it
  removes or renames before judging it", extension-scanner matchers, `[!!!]`
  prefixes, `checkRst` over a core diff. A triage writes no diff. The session
  used none of it.
- The cause is not the `audit` intent choosing badly. `typo3-core-issue-triage`
  is named by no intent at all, and no intent's `match` or `matchWeak` carries
  the word "triage". The same holds for `typo3-core-patch-checkout` and
  `typo3-extension-documentation`: nine of the twelve published skills are
  reachable from the guide and three are not.
- So the guide answered with the nearest intent that did match, which for
  read-only work is `audit`, whose `skill` is the extension conformance workflow
  and whose `skillCore` is the patch review one. Neither owns a triage.
- The session says the call still paid for itself on its suite and option
  blocks, and that a session trusting the routing field over the skill list
  "would have run an extension conformance review inside a core checkout".

## Decided

- A skill this repository publishes and the brief cannot name is a routing hole
  rather than a preference. The client selects on descriptions and the guide
  selects on intents, and a skill present in the first and absent from the
  second is reachable only by a caller who already knew it existed.
- So the set is held rather than curated by memory: every published skill is
  named by at least one intent, and a new skill that arrives without one fails a
  check instead of being discovered by a session that got the wrong workflow.
- What each of the three needs is not decided here. Triage is a task shape with
  its own vocabulary — tracker, Forge number, backlog, "is this still a thing" —
  and is a candidate for an intent of its own; the other two may belong on
  existing entries. That is the todo's reading.
- The checklist is a second finding on the same call and is not the same fix.
  `audit` returns removal, extension-scanner and changelog-file items to a task
  that produces no diff, which the domain withholding already does for hints.

## Assumed

- The three are the whole of it. Measured today against `skills/` and the intent
  file; a skill published under another mechanism would not be counted.
- Routing to the wrong skill costs more than routing to none. The session did
  not follow it, so what is measured is the wrong answer rather than its
  consequence.

## Wrong if

- A skill turns out to be deliberately unroutable — reached only from its own
  description because the guide cannot tell the task apart — which would make
  this a check with an exemption rather than an invariant.
- Giving triage its own intent is reported as pulling patch work into it, which
  would say the vocabulary does not separate the two.

## Since then

The intent names the skill, and the word people call the tracker by reaches it
weakly: the scope gate matches with `str_contains`, and the needle sits in the
triage intent's weak list. So the word is read as the tracker's name on both
gates and the repair is there. Against the feedback's own suggestion, which is
to move the triage clause to the other skill.
