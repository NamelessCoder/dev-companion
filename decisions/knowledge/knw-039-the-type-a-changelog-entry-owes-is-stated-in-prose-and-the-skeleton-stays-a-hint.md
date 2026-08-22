---
id: D-KNW-039
title: The type a changelog entry owes is stated in prose and the skeleton stays a hint
date: 2026-08-03
status: open
coveredBy:
  - KnowledgeTest::aChangelogQuestionIsToldWhichTypeTheChangeOwes
---

# D-KNW-039 — The type a changelog entry owes is stated in prose and the skeleton stays a hint

**The rule that picks between the four changelog types goes into the prose
section a changelog query already returns, and the skeleton stays a hint.**

One subject, two corpora, and the split is along what each one is asked from: a
session deciding whether the change is a `Feature` or an `Important` has not
opened the file yet and is asking the rules, while a session filling the file in
is in `Documentation/Changelog/` and is asking the hints.

## Evidence

- `Documentation/Changelog/Howto.rst` states the four types with a
  discriminating clause each, and its "Different changelog types" section is
  byte-identical in `.checkouts/12.4`, `13.4`, `14.3` and `main`.
  `Build/Scripts/validateRstFiles.php` is byte-identical across the same four
  and accepts only `Breaking|Deprecation|Feature|Important` in a title.
- No `Task-*.rst` exists in any of the four checkouts, while
  `knowledge/documents/typo3-commit-messages.md` and the `changelog` intent of
  `knowledge/task-intents.json` both named `Task-` as a filename prefix.
- The skeleton has been in `knowledge/hints/documentation.json` as
  `documentation-changelog` since 2026-07-28 and reads correctly against the
  four checkouts: the include, the anchor, the fenced title, the issue
  reference, the sections per type, and the `.. index::` tags with the scanner
  tag `Deprecation` and `Breaking` carry.
- The session behind `feedback/2026-08-02-145315` never reached it. It called
  `typo3_task_guide`, whose `changelog` intent named no tool at all and said to
  write the file "as in the neighbouring files", which is what it then did.

## Decided

- The type rule goes into `## Changelog Files` of
  `knowledge/documents/typo3-commit-messages.md`, the section a changelog query
  already returns at full coverage.
- Copying the skeleton into prose beside it was rejected. Two copies of one
  subject drift, and the hint reaches the session already editing the file in
  one call, which is the budget (`D-FBK-020`).
- The `changelog` intent names `typo3_hint_lookup` with
  `id=documentation-changelog`, so the route that was missing exists on the tool
  the failing session actually called.

## Assumed

- The four types and the validator stay the same across the covered branches. A
  statement that carries no version binding is what may stay prose at all
  (`R-KNW-023`), and this one is prose on that reading.

## Wrong if

- A fifth type appears, or one branch's validator accepts a different set. The
  statement is then version-bound and belongs in the hint corpus with the rest.
- A session asking `typo3_rule_lookup` a changelog question still reads a
  neighbouring entry to pick the type.
- The two corpora disagree — the hint's sections per type and the prose's type
  rule describing different files.
