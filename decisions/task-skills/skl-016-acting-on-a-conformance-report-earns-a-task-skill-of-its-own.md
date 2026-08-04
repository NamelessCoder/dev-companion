---
id: D-SKL-016
date: 2026-08-04
status: open
---

# D-SKL-016 — Acting on a conformance report earns a task skill of its own

**A task that asks for a repository to be put right gets a task skill of its
own: it starts from the conformance report, writes the findings into a worklist
it commits, and then works that list off.**

The change half of that request had one route, that route was the wrong one, and
removing it left the work with no owner at all.

## Evidence

- **Measured in this checkout on 2026-08-04.** `TaskIntents::detect()` against
  "look over my repository and put it right", "improve the code quality of my
  sitepackage", "clean up my extension and fix what is wrong" and "make my TYPO3
  project better" matches no intent, so `skills()` returns an empty list and
  `typo3_task_guide` names no skill. The same call with "review the TYPO3
  project and site package" returns `typo3-extension-conformance`: the intent
  works, and the wording is what it does not reach.
- **The `audit` intent cannot be widened into it.** Its needles are `audit`,
  `conformance`, `code review`, `review the`, `review this`, `review of` and
  `reviewing` — every one of them a word for looking, none of them a word for
  fixing.
- **The route that existed was wrong rather than missing.**
  `typo3-extension-conformance` opened its description "Review, audit, or
  improve a TYPO3 project…", and a client selects a skill on that line, so it
  was loaded for change requests whatever its body said (`D-SKL-014`, **Since
  then**). Taking the word out sent a change request into the workflow that
  exists to make none — which is what
  [`R-GUI-006`](../../requirements/guides/gui-006-a-review-is-not-answered-with-a-checklist-for-changing-something.md)
  holds — and the half it had been carrying went nowhere.
- **Conformance already names who takes each finding onward, and that is not
  what is missing.** Read on 2026-08-04: the skill owns "assessment and
  prioritization, and saying who takes each finding onward", names the workflow
  per finding whether or not fixes were requested, hands over for the changes
  and keeps itself responsible for re-checking. So the routing exists finding by
  finding. What no skill owns is the entry point for a request worded as a
  change, and the carrying-through — one list across many findings, in an order,
  survived across sessions.
- **What a session without a route does is on record twice, on other wordings.**
  `D-AUD-003`: a review prompt whose every criterion the conformance skill's
  body would have met did not activate it, and all thirty-five calls of that
  session went through Bash. The `E-EXT` run of 2026-07-31 behind
  [`R-SKL-009`](../../requirements/task-skills/skl-009-a-release-answer-is-about-the-archive-a-registry-receives.md):
  forty-one `Bash` calls, no skill activated and no tool called.

## Decided

- **A skill, and the two alternatives are rejected.** The card put three
  candidates to the maintainer and ruled a skill out in advance; that
  restriction was set aside by the person who queued it on 2026-08-04. An intent
  has nothing to route to, because no published workflow owns "the whole
  repository", and widening another skill's `description` until the words fall
  into it is how the removed word got there in the first place.
- **Conformance is the precondition and stays analysis.** The skill does not
  re-derive the findings and does not give the audit a change step: it starts
  from the report `typo3-extension-conformance` produces, which `R-GUI-006`
  keeps free of a patch checklist.
- **The worklist is written down and committed before any of it is worked.**
  What the audit found becomes an ordered list of its own, and the commit that
  carries it is what a session interrupted halfway comes back to.
- **What it adds is the entry point, the order and the carrying-through.** Which
  workflow owns a finding is already conformance's answer and is not restated
  here; what this skill contributes is being reachable from a change-worded
  request, turning a report into a list somebody can work off, and staying with
  that list until it is empty.
- **Each item crosses into the skill that owns it** rather than being carried
  here, which is
  [`R-SKL-003`](../../requirements/task-skills/skl-003-crossing-into-another-skills-work-is-an-explicit-transition.md).
- **The route is the second half of publishing rather than part of writing it.**
  `knowledge/task-intents.json` may name no skill before it is in
  `Installer::SKILLS`, which `SkillTest::everySkillNamedInKnowledgeIsPublished`
  holds — the shape `typo3-development-installation` has been in since
  `D-SKL-013`.

## Assumed

- **That the wording arrives at all.** No filed session has brought it, which is
  why the card is `normal`, and
  [writing-a-skill.md](../../documentation/clients/writing-a-skill.md) settles a
  domain with a scenario case or a recorded run rather than with a shape.
  Standing in for one is a hole this repository's own decision made: the change
  half of a route it removed.
- **That the conformance report carries enough to derive a list from.** Nothing
  measures whether what the audit returns is ordered or specific enough to
  become items somebody works off.
- **That committing the list before the work is what is wanted of it**, rather
  than one commit at the end. It is what was asked for and no run has been
  through it.

## Wrong if

- The draft turns out to re-derive what conformance already found. Then it is
  the audit with an edit step on the end, and the two are one skill rather than
  two.
- A session loads it and works the list without activating the skills that own
  the items. Then it is a second copy of every workflow, which is what
  `R-SKL-003` exists against.
- The conformance report turns out not to carry findings a list can be derived
  from. Then the precondition is a boundary rather than a hand-over, and what
  has to change is what the audit returns.
- The skill is published and no session loads it, because nobody words the
  request this way. Then the shape was hypothesised, and the bar
  `writing-a-skill.md` sets was the thing to wait for.
