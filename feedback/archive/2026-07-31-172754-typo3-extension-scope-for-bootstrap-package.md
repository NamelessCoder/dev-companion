---
date: 2026-07-31T17:27:54+00:00
category: wrong-answer
status: closed
closed: 2026-08-02
tool: typo3_extension_scope
directory: /home/benji/projects/bootstrap_package
---

# typo3_extension_scope for bootstrap_package reported "Classes: ... Updates (27)". The checkout ac...

## Observation

typo3_extension_scope for bootstrap_package reported "Classes: ... Updates (27)". The checkout actually contains 21 files under Classes/Updates/ (AbstractUpdate.php plus 20 concrete wizard classes). The count overstated the wizard surface, and the follow-up "Ships" section did list the real test files, so the wrong number was only mildly misleading — but a count read from files should match the file listing.

## Query

extension=bootstrap_package

## Suggestion

Derive the per-subdirectory class counts from the actual file listing so "Updates (N)" equals the number of files under Classes/Updates/. Consider distinguishing abstract base classes from concrete wizards.
