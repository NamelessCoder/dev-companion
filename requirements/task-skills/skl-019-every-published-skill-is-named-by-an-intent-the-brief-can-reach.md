---
id: R-SKL-019
status: open
restsOn: [D-SKL-023]
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

## Held by

- `not guarded` — three skills fail it today, so a test would be red on arrival.
  The todo that gives them intents writes the check in the same commit, which is
  what turns this entry from `open` into one nobody can quietly regress.
