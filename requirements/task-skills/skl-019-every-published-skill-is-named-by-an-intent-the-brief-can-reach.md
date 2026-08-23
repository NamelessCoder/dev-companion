---
id: R-SKL-019
title: 'Every published skill is named by an intent the brief can reach'
status: held
restsOn: [D-SKL-023]
heldBy:
  - SkillTest::aCoreTriageReachesTheSkillThatOwnsItWithoutNamingAPath
  - SkillTest::everyPublishedSkillIsNamedByAnIntent
---

# R-SKL-019 — Every published skill is named by an intent the brief can reach

**Every skill this repository publishes is named by at least one entry in
`knowledge/task-intents.json`.**

A client selects a skill on its description; `typo3_task_guide` selects one on
the intents. A skill in the first and not the second is reachable only by a
caller who already knew it existed, and the guide answers such a task with the
nearest intent that did match — which is a different workflow, confidently
named.

## From

`feedback/2026-08-07-233443`, 2026-08-07. A core triage described as "Triage an
old open core bug report" was answered `skills: ["typo3-extension-conformance"]`
with a patch-review checklist, in a checkout reported as `core-checkout` with no
project-own extensions. `typo3-core-issue-triage` owns that task and is named by
no intent; nor are `typo3-core-patch-checkout` and
`typo3-extension-documentation`. Nine of twelve were reachable.

**Built on 2026-08-08.** `triage` and `patch-checkout` are intents of their own,
core-scoped the way `submission` is, and `changelog` names
`typo3-extension-documentation` outside the core and
`typo3-core-patch-development` inside it. Twelve of twelve are reachable.

Reachable took a second half. "Triage an old open core bug report" carries none
of the markers `Scope::isCoreWork` reads, so a core-scoped intent was demoted to
weak on the very call that found the hole, and every intent that did match
answered with its extension side. The work that ends before a patch names the
core as a tracker and a checkout — `core issue`, `core bug`, `core checkout`,
`core backlog`, `core tracker` are markers now.

## Held by

The first has one exemption and it is in the code rather than in a list: a draft
is not published, and a draft reachable by routing is one nobody chose.
