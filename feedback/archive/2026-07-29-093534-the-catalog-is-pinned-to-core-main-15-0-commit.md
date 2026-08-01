---
date: 2026-07-29T09:35:34+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 2fa99e8
subject: "Contrast the version the answer describes with the one being read"
tool: typo3_component_lookup
---

# The catalog is pinned to core main / 15.0 (commit 4c8b38b2, verified 2026-07-28) and every answer...

## Observation

The catalog is pinned to core main / 15.0 (commit 4c8b38b2, verified 2026-07-28) and every answer carries that markup with no version qualifier. bootstrap_package requires typo3/cms-core ^13.4 || ^14.3 and ships ~15 backend preview templates under Resources/Private/Templates/Preview/. Card markup, sub-component class names and the --typo3-card-* custom property contract are exactly the kind of thing that shifted between 13.4 and 15.0, so the returned markup cannot be pasted into a template that has to render in both supported versions. typo3_catalog_scope reports the pin honestly, but the component payload itself gives no hint that it may be ahead of the branch the caller supports.</observation>
<parameter name="suggestion">Carry version information per component — at minimum a "since" field, better a note when a class or custom property changed within the still-supported 13.4/14.x range. Alternatively let the caller pass targetVersions and either annotate or withhold what is not verified for them. A cheap first step: repeat the catalog pin inside every component entry, not only in the trailing catalog block, and word the miss case as "not in the 15.0 snapshot" wherever it is surfaced.</suggestion>
</invoke>

## Query

typo3_component_lookup {"query":"content element backend preview"} — from an extension supporting TYPO3 13.4 and 14.3
