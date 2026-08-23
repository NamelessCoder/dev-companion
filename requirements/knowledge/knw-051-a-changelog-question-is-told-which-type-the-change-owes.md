---
id: R-KNW-051
title: 'A changelog question is told which type the change owes'
status: held
restsOn: [D-KNW-039]
heldBy:
  - KnowledgeTest::aChangelogQuestionIsToldWhichTypeTheChangeOwes
---

# R-KNW-051 — A changelog question is told which type the change owes

**A caller asking this server about a changelog entry is told which of the four
types the change owes, by the rule that separates them.**

Where the file goes and what it is named are mechanical, and a session that gets
them wrong is told so by `checkRst`. The type is the one part no check reports:
a `Feature` file where the change was `Important` passes every suite and is
found in review, or not at all. So the discriminating clause per type is what
the answer has to carry — not the list of four, which says nothing about which
one is being written.

## From

`feedback/2026-08-02-145315` (2026-08-02), a session that produced a core patch
for Forge #105403 and settled the type by reading neighbouring entries, having
been told by the `changelog` intent of `knowledge/task-intents.json` to write
the file "as in the neighbouring files". The same intent and
`knowledge/documents/typo3-commit-messages.md` both named a `Task-` prefix that
no branch's `Build/Scripts/validateRstFiles.php` accepts and no entry in
`.checkouts/12.4`, `13.4`, `14.3` or `main` carries.
