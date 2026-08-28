---
id: D-SCO-003
title: What is core-only is decided per line, by what it names
date: 2026-07-29
status: confirmed
---

# D-SCO-003 — What is core-only is decided per line, by what it names

**Whether a line is core-only is a mechanical check over the rendered text
rather than a flag on each entry.**

`typo3_task_guide` now drops core-only material outside the core. What counts as
core-only is not a flag on each entry but a check on its text: does it name
something that exists in the core repository and nowhere else — `typo3/sysext/`,
`Build/Scripts/`, Gerrit, a Change-Id, the core branch policy.

## Decided

- A mechanical check over the rendered line, in `Scope`, applied to the
  checklist, the checkout discovery steps and the follow-up tools. The
  alternative — marking every checklist item, every intent item and every scope
  entry in the knowledge files — is a flag on a hundred strings that has to be
  set correctly each time one is added, and forgetting it fails silently.

## Assumed

- Naming a core artefact is a reliable proxy for being unusable outside the
  core, and the cost of the two error directions is asymmetric: a transferable
  line dropped because it mentioned a core path as an example is a smaller loss
  than an unrunnable command handed over as a step.

## Wrong if

- A checklist item has to survive although it names a core path — advice about
  reading the core as a reference rather than changing it would be exactly that.
  It would then need the flag after all.

## Confirmed on 2026-08-02

The **Wrong if** has not happened. Of the three corpora the check runs over in
`TaskGuide`, six lines drop and all six instruct writing into the core or
pushing to it, so the shape that would need a flag is not in the corpus.

## Confirmed on 2026-08-28

The **Assumed** failed in the other direction, which this entry named as the
costlier one: a line that is core-only and names no core artefact was handed to
an extension session. `D-SCO-015` has the measurement and the repair, and the
mechanical check keeps its case.
