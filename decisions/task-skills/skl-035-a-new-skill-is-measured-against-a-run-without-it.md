---
id: D-SKL-035
title: A new skill is measured against a run without it
date: 2026-08-12
status: open
---

# D-SKL-035 — A new skill is measured against a run without it

**A new skill buys a baseline run — the same task, in an environment where it is
not installed — and an edit to a published one does not.**

That a domain earned a skill is the first of the three authoring steps nothing
reads off a file. It is the one a run can hold, and the only one where the cost
buys a decision rather than a reassurance.

## Evidence

- obra/superpowers state it as an Iron Law: no skill without a failing test
  first, the test being the task run without the skill. They extend it to edits.
  `czlonkowski/n8n-skills` writes the scenarios before the `SKILL.md`, and
  Anthropic's own `skill-creator` spawns the baseline and the with-skill run in
  one turn. Read on 2026-08-08.
- What makes it more than a slogan is that superpowers measured their own file:
  deleting one section moved test-first behaviour from 8/10 to 5/10,
  corroborated on two models, and the section came back in a different form.
- Superpowers price a sample at roughly $0.15–0.30 and run five or more per
  variant, so one measured change is a few dollars and a session's attention.
- Three quarters of the mechanism is already here: `scenarios/contracts/` holds
  the prompts, `scenarios/runs/` records a graded run, `documentation/records/`
  carries the debrief, and `.environments/` makes a checkout without the skills
  installed cheap to produce. The missing half is the run without the skill.

## Decided

- A new skill is published with a baseline run behind it: the case prompt run in
  an environment the skills are not installed into, recorded beside the
  with-skill run it is compared against.
- An edit stays on the author's word, as all three authoring steps do today. The
  cost is per change and the benefit is unmeasured, so the rule is taken where a
  domain is being claimed and nowhere else.
- Rejected: the Iron Law whole. Charging every edit a handful of paid runs is a
  standing tax on the cheapest kind of improvement — a sentence that was wrong —
  and it is what stops the sentence being fixed.

## Assumed

- That the step which goes wrong is the domain rather than the wording. Nothing
  here has measured a published skill made worse by an edit, which is what would
  price the other half.
- That an environment without the skills installed stays cheap. It is a checkout
  the installer was not run in, and nothing else.

## Wrong if

- An edit degrades a published workflow and nobody notices, which is precisely
  what this declines to guard.
- The baseline run for a new skill is skipped, because nothing reads it off a
  file either and the rule is one more sentence in `writing-a-skill.rst`.
- A sample costs materially more than the price above, so a new skill is a
  decision about budget rather than about evidence.

## Since then

Two skills were published on 2026-08-19 without one.
`typo3-extension-patch-review` and `typo3-distribution-content` had the review
`writing-a-skill.rst` requires and no run of any kind, and the maintainer
published on the review alone — `D-SKL-064`. So what this entry decided has been
overruled once, in the open, and the first thing that would show it right is a
finding in either skill that a baseline run would have caught.
