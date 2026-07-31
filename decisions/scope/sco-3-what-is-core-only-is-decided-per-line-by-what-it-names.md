---
id: D-SCO-3
date: 2026-07-29
status: standing
---

# D-SCO-3 — What is core-only is decided per line, by what it names

**Whether a line is core-only is a mechanical check over the rendered text —
does it name something that exists in the core repository and nowhere else —
rather than a flag on each entry.**

`typo3_task_guide` now drops core-only material outside the core. What counts
as core-only is not a flag on each entry but a check on its text: does it name
something that exists in the core repository and nowhere else —
`typo3/sysext/`, `Build/Scripts/`, Gerrit, a Change-Id, the core branch policy.

- **Decided:** a mechanical check over the rendered line, in `Scope`, applied
  to the checklist, the checkout discovery steps and the follow-up tools. The
  alternative — marking every checklist item, every intent item and every scope
  entry in the knowledge files — is a flag on a hundred strings that has to be
  set correctly each time one is added, and forgetting it fails silently.
- **Assumed:** naming a core artefact is a reliable proxy for being unusable
  outside the core, and the cost of the two error directions is asymmetric: a
  transferable line dropped because it mentioned a core path as an example is a
  smaller loss than an unrunnable command handed over as a step.
- **Wrong if:** a checklist item has to survive although it names a core path —
  advice about reading the core as a reference rather than changing it would be
  exactly that. It would then need the flag after all.
