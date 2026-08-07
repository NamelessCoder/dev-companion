---
id: R-ANS-026
status: open
restsOn: [D-ANS-060]
---

# R-ANS-026 — A path names the subsystem, and a hint from another one does not answer

**A hint lookup given a path under `typo3/sysext/extbase/Classes/Persistence/`
returns `persistence-reading` and does not return `fal-storages-drivers`.**

A caller that names a path has said which subsystem the question is about, in
the least ambiguous way the interface offers. An answer that hands back another
subsystem's conventions is worse than an empty one: the call exists to give the
conventions before a view of the code is formed, and a plausible set from
somewhere else is read as that step having happened.

## From

Two sessions in one core checkout on 2026-08-07. `feedback/2026-08-07-132426`
got `fal-storages-drivers` from `typo3_hint_lookup` with three Extbase
persistence paths, with `persistence-reading` and `extbase-domain-mapping`
sitting unreturned in `availableHints`; `feedback/2026-08-07-065259` got the
same from `typo3_task_guide` with the same paths, hours earlier and in another
task. The cause is the bare `storage` in the FAL hint's `appliesTo`, matched
against the `Storage/` segment of the path as a prefix, and ranked above the
hints that answer because `keywords` sorts before `score`.

## Held by

- `not guarded` — the regression check is the todo's work, and the request for
  it is in the feedback that reported it.
