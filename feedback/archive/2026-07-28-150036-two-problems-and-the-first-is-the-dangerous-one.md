---
date: 2026-07-28T15:00:36+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: ef5434f
subject: "Cover all three places the core registers an icon"
tool: typo3_icon_lookup
---

# The icon catalog is built from icons.json alone, so extension-registered icons and the flags family are invisible

## Observation

Remaining half of the original note. The silent fuzzy fallback it also reported
is fixed: an identifier-shaped query that finds no exact hit now says so and
labels the fuzzy results as suggestions rather than results.

Still open is the coverage gap behind it. The snapshot is built from icons.json
alone. Icons a system extension registers through Configuration/Icons.php are
missing — 12 are registered across 5 sysexts at the pinned revision, 7 of them
absent from the snapshot: provider-bitmap, provider-svg, status-reference-hard,
status-reference-soft, tcarecords-tx_styleguide_forms-default,
theme-camino-content-socialmedia, theme-camino-record-listitem. The entire
flags-* family is invisible as well, because flags live in
typo3/sysext/core/Resources/Public/Icons/Flags and are registered lazily by
IconRegistry::initializeFlags(); flags-multiple is used in production code
(typo3/sysext/beuser/Classes/Service/UserInformationService.php:281) and cannot
be validated with this tool. Measured against all 260 icon identifiers actually
referenced in core code, the snapshot covers about 96 percent — but the gap sits
exactly in the extensions an agent is likely to be patching.

## Query

query="status-reference-hard", query="flags-multiple"

## Suggestion

Extend the catalog build to also read every
typo3/sysext/*/Configuration/Icons.php and the flag SVGs under
typo3/sysext/core/Resources/Public/Icons/Flags, and record per identifier where
it comes from (T3Icons, extension registration, flags), so a miss can be
attributed instead of guessed and typo3_catalog_scope can state the three
sources separately.
