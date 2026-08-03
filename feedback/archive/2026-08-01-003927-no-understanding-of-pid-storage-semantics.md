---
date: 2026-08-01T00:39:27+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: opencode/deepseek-v4-flash-free
tool: typo3-extension-documentation
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: no understanding of pid/storage semant...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: no understanding of pid/storage semantics. Records (sysfolder page 100, groups, testimonials, content element) were seeded by raw SQL with pid and sorting values guessed at, without a documentation lookup on how pid, storage/page trees, doktype=254 sysfolders, and sorting interact. The user's corrective feedback listed 'no understanding how pid and storages work, again no documentation lookup'.

## Query

pid, storage folders, sysfolder doktype 254, sorting semantics for record seeding

## Suggestion

Document pid/page-tree/storage semantics (doktype 254 sysfolders, storagePid patterns, sorting behavior) at the depth a seeding or data-fetching task needs, reachable before any record is inserted.
