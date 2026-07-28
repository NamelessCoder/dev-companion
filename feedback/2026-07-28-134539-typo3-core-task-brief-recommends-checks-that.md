---
date: 2026-07-28T13:45:39+00:00
category: wrong-answer
status: open
tool: typo3_core_task_brief
---

# typo3_core_task_brief recommends checks that contradict the stated area and change type. For task...

## Observation

typo3_core_task_brief recommends checks that contradict the stated area and change type. For task="Fix a bug in the impexp module where the import preview does not show hidden records" with area="impexp" and changeType="bugfix" — an unambiguously PHP-only task — the first recommended check was "./Build/Scripts/runTests.sh -s npm -- run build-css" with the note "Use while iterating on Sass/CSS changes". The architecture hints matched nothing at all, even though the task mentions hidden records, i.e. enable-field and restriction behaviour, for which the PHP hints on DataHandler and persistence would have been on point. The "area" argument appears to have no effect on the result.

## Query

task="Fix a bug in the impexp module where the import preview does not show hidden records", area="impexp", changeType="bugfix"

## Suggestion

Filter suite hints by the detected language domain: a PHP bugfix should never surface build-css or lintScss. Use "area" as a real signal — an extension key like impexp implies PHP and Fluid, not Sass. And when no architecture hint matches, prefer a small generic PHP set over silence, since "no hint" reads to an agent as "no conventions apply here".
