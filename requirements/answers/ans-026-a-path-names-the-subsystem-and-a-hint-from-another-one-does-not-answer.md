---
id: R-ANS-026
status: held
restsOn: [D-ANS-060]
---

# R-ANS-026 — A path names the subsystem, and a hint from another one does not answer

**A hint lookup given a path under `typo3/sysext/extbase/Classes/Persistence/`
does not answer with the conventions of another subsystem.**

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

The positive half is deliberately absent. Which hint *should* answer for an
Extbase persistence path is a subject the corpus does not carry, established on
2026-08-07 by reading both candidates: `persistence-reading` is the core
QueryBuilder, `PageRepository` and the restrictions, and
`extbase-domain-mapping` is the model and the table behind it. Neither covers
the query parser, the column map or `Backend`. The requirement therefore demands
silence over a wrong subsystem rather than a named hint, until there is one.

## Held by

- `HintsTest::anExtbasePersistencePathIsNotAnsweredWithAnotherSubsystem`
- `HintsTest::pruningThePathPatternsLeftBothSubjectsReachable`
