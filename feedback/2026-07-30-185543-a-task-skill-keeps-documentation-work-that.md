---
date: 2026-07-30T18:55:43+02:00
category: tool-gap
status: open
tool: typo3_task_guide
---

# A task skill keeps documentation work that another skill owns. In a forward run

## Observation

A task skill keeps documentation work that another skill owns. In a forward run
of EXT-04 the backend-module skill activated and stayed the only active one for
the whole session. When it came to writing down what it had built, it edited the
project's readme: it updated passages that were already correct, carried
implementation detail into a project-level document, and left the wording mixed
between two languages because the labels it had written were.

The extension's own readme was not touched, although the functionality is
encapsulated in that package and belongs there. The backend-module skill names
the documentation skill as the owner of manuals, and the documentation skill was
never activated — so the ownership sentence describes a hand-off that does not
happen.

## Query

document the backend module that was just built

## Suggestion

Make the hand-off an act rather than a sentence: name the moment a skill has to
stop and which skill takes over, and say that documentation for an extension
belongs to that extension. Cover it with a forward scenario in which a task that
starts in one skill has to end in another, so the boundary is measured rather
than declared.
