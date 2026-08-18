---
date: 2026-08-18T07:43:05+00:00
category: idea
status: closed
closed: 2026-08-18
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_task_guide, typo3_commit_message_guide
directory: /home/benji/projects/blog
---

# What worked: project_describe as the first call, the operations brief, and the commit message guide

## Observation

Task: "ich habe das blog setup ausgeführt, aber das frontend zeigt immer noch 404" on a DDEV TYPO3 14.3.6 installation of t3g/blog, which grew into three commits. Filing what worked so it is not broken later.

typo3_project_describe as the first call was worth more than any other single answer. One call, no arguments, and it named both configured sites with their bases and rootPageIds, the DDEV environment and docroot, and the commands the repository actually declares with each marked check or change. The two sites were the bug; I had the shape of it before opening a single file. The check/change marking also decided what I ran unasked: I ran composer test:php:lint and composer phpstan freely because they were marked check, and they caught two real PHPStan errors in code I had just written.

typo3_task_guide with changeType operations returned a brief with an actual shape rather than a patch checklist, which is the right distinction. Two things in it paid off directly. The DDEV hints on config/system/additional.php ownership stopped me from touching a generated file to "fix" the site — I left DDEV's settings management alone and the fix turned out to be elsewhere entirely. And the checklist item about a seeded installation answering 404 at its own root because of a base put me on site configuration within the first minute; the mechanism turned out to be a different one, but the class of cause was right and it saved me from chasing TypoScript or caches.

One caveat worth recording rather than filing separately: of the seventeen checklist items, six were guarded "only if the task is setting an installation up rather than working on the code in one", which did not apply — I was repairing an existing one. They were correctly labelled, so the guard worked; it is just a lot of answer to read past.

typo3_commit_message_guide, three calls, workflow project. Every one produced a usable message and one returned summary-length-preferred on a 62-character subject, which I shortened before committing. Passing changeType plus summary plus body and getting back a wrapped, checked message is exactly the right shape for this: no round trip spent guessing the conventions.

## Query

Order of calls in the session: typo3_project_describe (no args); typo3_task_guide (task "Blog extension development installation was set up, backend works but frontend returns 404 page not found", changeType operations, targetVersion 14.3, three paths); typo3_changelog_lookup ("TcaSchemaFactory"); typo3_commit_message_guide three times with workflow project.

## Suggestion

Keep all three as they are. If anything changes in typo3_task_guide, keep the guarded checklist items guarded in the same words — the guard is what let me skip them safely — and keep the check/change/unknown marking in typo3_project_describe, which is what makes a caller under an instruction not to change files willing to run the repository's own linters.
