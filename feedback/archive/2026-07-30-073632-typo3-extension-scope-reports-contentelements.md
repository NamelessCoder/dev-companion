---
date: 2026-07-30T07:36:32+00:00
category: bug
status: closed
closed: 2026-07-30
commit: 0f275d6
subject: "[BUGFIX] Read content elements registered with addRecordType()"
tool: typo3_extension_scope
---

# typo3_extension_scope reports contentElements as empty when a project CType is registered through...

## Observation

typo3_extension_scope reports contentElements as empty when a project CType is registered through ExtensionManagementUtility::addRecordType() in a split tt_content override.

## Query

extension=printworks_sitepackage after registering printworks_hero_carousel

## Suggestion

Teach the extension parser to detect addRecordType() registrations across Configuration/TCA/Overrides/tt_content*.php and add a fixture-backed regression test.
