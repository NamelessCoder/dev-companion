---
date: 2026-08-01T00:32:16+00:00
category: missing-knowledge
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development, typo3-extension-documentation
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session: the assistant lacked DataHandler knowledge and work...

## Observation

Debrief of the TYPO3 14 testimonials session: the assistant lacked DataHandler knowledge and worked around it instead of using the documented API. To seed live-DB records (sysfolder page, two testimonial groups, five testimonials, and a tt_content element) it issued raw INSERT statements directly through mysql instead of TYPO3's DataHandler (TCEmain), so the records bypassed the API: IRRE relations had to be written onto the child 'parent' column by hand after discovering the group's 'testimonials' column is an int that rejects a comma list, pid/sorting semantics were guessed at, and the assistant lost track of which records were manually modified. It never consulted the documentation on DataHandler or the recommended import/seed mechanisms (e.g. impexp) even though typo3/cms-impexp is installed in the project. The user listed 'did not use the datahandler, for database operations' in their own corrective feedback.

## Query

seeding records via DataHandler instead of raw SQL inserts; IRRE parent relations; impexp import

## Suggestion

Provide, in the task/extension documentation guidance: that record creation, updates, and relations must go through the DataHandler (TCEmain) API, how IRRE relations are written (children's parent field, not a comma list on the parent), and the impexp/recommended seeding flow — so a seeding task never reaches for raw SQL.
