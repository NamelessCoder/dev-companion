---
date: 2026-07-28T15:03:44+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 4104ca5
subject: "Fill the component catalog gaps the audit found"
tool: typo3_component_lookup
---

# An audit against the catalog's exact pinned core commit 4c8b38b2dd07 found that all declared Sass...

## Observation

An audit against the catalog's exact pinned core commit 4c8b38b2dd07 found that all declared Sass/demo paths exist and the catalogued classes/custom properties are generally valid, but the 22-entry component catalog is substantially incomplete compared with 76 Sass component files and 30 styleguide component templates. Concrete misses or misleading fallbacks: dropzone returns no match although Build/Sources/Sass/component/_dropzone.scss exists; note returns Tree solely via the node-note subcomponent although Build/Sources/Sass/component/_note.scss exists; status indicator returns Badge although Build/Sources/Sass/component/_status-indicator.scss exists. The input entry also lists form-text as a subcomponent while its single reported Sass path _form-control.scss does not contain it; the actual source is forms/_form-text.scss.

## Query

dropzone; note; status indicator

## Suggestion

Add high-value existing components such as dropzone, note and status-indicator, or generate a complete searchable source index alongside the curated detailed entries. Prefer exact component/source-name matches over keyword or subcomponent matches. Support multiple Sass source paths where one catalog entry spans form-control, form-label, form-text and input-group files, and explicitly label fallback results as related rather than canonical.
