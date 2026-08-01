---
date: 2026-07-28T14:54:21+00:00
category: bug
status: closed
closed: 2026-07-28
commit: 4e41aed
subject: "Separate what a task says from what it is about"
tool: typo3_core_task_brief
---

# The word "label" in the task text triggered the "labels"/xliff intent even though the task is abo...

## Observation

The word "label" in the task text triggered the "labels"/xliff intent even though the task is about TCA field labels rendered by FormEngine, not about XLF trans-units. Two consequences: (1) the returned rules section was entirely the XLIFF Label Lifecycle plus Language Files, which is irrelevant for this change; (2) the top-level "checks" array contained only checkIntegrityXliff and normalizeXliff, while the matched architecture hint "tca-formengine" carries the actually relevant check (functional). The real upstream fix for this exact bug (commit 99669172ad8) touched typo3/sysext/core/Configuration/DefaultConfiguration.php and added a functional test — no XLF file at all. An agent following the brief's "checks" would run the wrong suites and skip the one that matters. The checklist showed the same drift: four of its eleven items were about XLF labels.

## Query

task="Fix that TSconfig field label overrides are not respected per record type in FormEngine select fields", area="backend/FormEngine", changeType="bugfix"

## Suggestion

Two things. First, the top-level "checks" should be the union of the intent checks and the checks of every matched architectureHint, so functional cannot be dropped while tca-formengine is matched. Second, the "labels" intent should not fire on "label" alone when the task also matches a subsystem like TCA/FormEngine — require an XLF/XLIFF/trans-unit/translation signal, or demote it to a secondary intent whose rules and checklist items are marked as conditional ("only if you add or change XLF labels").
