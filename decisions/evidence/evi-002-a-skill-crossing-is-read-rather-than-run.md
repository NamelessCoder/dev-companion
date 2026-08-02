---
id: D-EVI-002
date: 2026-07-31
status: confirmed
---

# D-EVI-002 — A skill crossing is read rather than run

**That a session actually hands over from one task skill to another is held by
the skill text and by the contract case read by hand.**

No forward run will be scheduled to produce it.

Every other half of `R-SKL-003` has evidence. This one asked for a run that cannot
exist, and the feedback asking for it stayed open for that reason alone.

## Evidence

- The fourth `REVIEW-01` run shows a session routing its findings to the
  `typo3-extension-documentation`, `typo3-content-element-development` and
  `typo3-extension-testing` workflows by name, and keeping conformance
  responsible for re-checking them. Naming the next owner is therefore
  evidenced. What no run shows is the activation itself — a review stops at
  findings by design, so `REVIEW-01` cannot produce it however often it is run.
  Re-read on 2026-07-31, the skill still carries the transition in the order
  the test asserts: implementation verified, workflow stopped, documentation
  skill activated, extension scope carried across.

## Decided

- The crossing stays held by
  `SkillTest::backendModuleDocumentationIsAnExplicitSkillTransition` and by
  `SKILL-07` read by hand, and `R-SKL-003` says so in as many words. Rejected: a
  fourth forward scenario shaped as an implementation task. `D-EVI-001` admits
  only an open review as forward evidence, and a prompt broad enough to qualify
  cannot be relied on to reach this boundary at all — one that could would be
  naming the route, which is the thing that decision was written against.

## Assumed

- That the wording decides the behavior — that an assertion on the order of
  four sentences in `SKILL.md` stands in for what an agent does with them. That
  is a proxy, and it is the only one available.

## Wrong if

- A forward run that happens to cross the boundary shows the session editing
  documentation with the backend-module skill still the only active one. The
  wording would then be present and inert, the proxy worth nothing, and
  `R-SKL-003` back to needing evidence this decision says it will not get.

## Confirmed on 2026-08-02

The **Wrong if** has not happened, and nothing that ran could have produced it.
Read out of the client transcripts for the four checkouts that play `E-SITE`
and `E-EXT`, 14 sessions have run there since the transition wording landed in
`f9e9639` on 2026-07-30 at 17:35 UTC. Not one of the 14 edited a file: 11
activated `typo3-extension-conformance` and nothing else, three activated no
skill at all. `typo3-backend-module-development` has activated once in those
checkouts ever, on 2026-07-30 at 16:20:17 in `site-new` — 75 minutes before the
wording existed. That session edited the project-level `readme.md` at 16:40:29
with no second skill activated, which is the run `R-SKL-003` was written **From**
and not new evidence. What it settles is that the reading is cheap where there
is something to read: the failing shape is a `Skill` call and an `Edit` path in
one transcript with nothing between them. What is missing is a run. The
sessions these environments now get are `REVIEW-01` and `REVIEW-02`, and a
review neither edits — `D-EVI-003` — nor reaches this skill. So the **Wrong if**
is out of a forward run's reach for the same reason the activation is, and what
would produce it is an implementation prompt: `SKILL-07`, a contract case. The
wording is at least where it would have to act. Re-read on 2026-08-02 the skill
still carries the transition in the order the test asserts, and the copy
published into each of the four checkouts is identical to it.
