---
date: 2026-07-28T15:00:36+00:00
category: wrong-answer
status: open
tool: typo3_icon_lookup
---

# Two problems, and the first is the dangerous one. (1) Silent fuzzy fallback: status-reference-har...

## Observation

Two problems, and the first is the dangerous one. (1) Silent fuzzy fallback: status-reference-hard is a real registered identifier in the checkout (typo3/sysext/impexp/Configuration/Icons.php) but is absent from the snapshot. Instead of reporting a miss, the tool returned 21 fuzzy matches led by status-dialog-error, all with a plausible-looking "why". An agent asking "is status-reference-hard valid?" gets back a confident wrong substitute. Compare query="passkey", which correctly returned matchCount 0 plus the concept list — that is the useful behaviour. The difference is that any shared name part is enough to produce matches, so an exact-looking query silently degrades into a suggestion list. (2) Coverage: the snapshot is icons.json only. Icons registered by extensions through Configuration/Icons.php are missing (12 registered across 5 sysexts at the pinned revision, 7 of them absent from the snapshot: provider-bitmap, provider-svg, status-reference-hard, status-reference-soft, tcarecords-tx_styleguide_forms-default, theme-camino-content-socialmedia, theme-camino-record-listitem). And the entire flags-* family is invisible: icons.json contains zero flags-* entries because flags live in typo3/sysext/core/Resources/Public/Icons/Flags and are registered lazily by IconRegistry::initializeFlags(). flags-multiple is used in production code (typo3/sysext/beuser/Classes/Service/UserInformationService.php:281) and cannot be validated with this tool. Measured against all 260 icon identifiers actually referenced in core code, the snapshot covers about 96 percent — good, but the gap is concentrated exactly in the extensions an agent is likely to be patching.

## Query

query="status-reference-hard" (an identifier that exists in the checkout) and query="passkey" (one that does not)

## Suggestion

Separate exact validation from discovery. When the query looks like a full identifier (matches the ^(actions|apps|content|flags|form|mimetypes|module|overlay|status|tcarecords|provider|theme)-[a-z0-9-]+$ shape) and there is no exact hit, say so explicitly — an "exactMatch": false field plus a note that the snapshot may lag the branch and a pointer to typo3_catalog_status and typo3_make_me_better — before listing fuzzy alternatives. Fuzzy results for an exact-shaped query should be clearly labelled as suggestions, not results. For coverage, extend the catalog build to also read every typo3/sysext/*/Configuration/Icons.php and the flag SVGs under typo3/sysext/core/Resources/Public/Icons/Flags, and record per-identifier where it comes from (T3Icons, extension registration, flags), so a miss can be attributed instead of guessed.
