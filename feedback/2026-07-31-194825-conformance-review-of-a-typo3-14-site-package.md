---
date: 2026-07-31T19:48:25+00:00
category: idea
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3_extension_scope
directory: /home/benji/projects/site-new
---

# Conformance review of a TYPO3 14 site package: typo3_extension_scope answered absences as first-c...

## Observation

Conformance review of a TYPO3 14 site package: typo3_extension_scope answered absences as first-class results — no manual, no README, which test layers exist and which do not — and read the XLF source languages; typo3_project_scope returned the commands the repository actually declares, with what each does to the sources. That let the review distinguish "missing" from "not yet read" and stopped me from recommending check scripts the repository does not have.

## Query

typo3_extension_scope extension="printworks_sitepackage"; typo3_project_scope

## Suggestion

Keep answering absences explicitly and keep the answeredBy attribution; both are what make the answers trustworthy without reading every directory.
