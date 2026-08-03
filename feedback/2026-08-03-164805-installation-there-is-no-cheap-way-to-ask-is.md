---
date: 2026-08-03T16:48:05+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_documentation_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Trimmed on 2026-08-03 to the part that is left. Two of the three instances are
settled, and the field the proposal rests on has one value.

The deprecated one is reached by its own name. `typo3_changelog_lookup` with
`query: "InfoboxViewHelper STATE_ERROR"` returns `#107648` alone and says
`removed in v15.0` — the identifier route landed with `D-ANS-042`, the removal
with `D-ANS-020`. The sweep by `type`, `version` and `tag` is what missed it.
The `@deprecated` docblock against a `#[\Deprecated]` attribute divides nothing
here: the attribute occurs zero times in `typo3/sysext` on `.checkouts/12.4`,
`13.4`, `14.3` and `main`, and zero times in the audited installation.
`D-ANS-010` carries the readings.

The severity is settled and `deprecated-apis` states it: a class constant is read
without anything in the declaring class running, so no `trigger_error` can be
attached to one anywhere, and the extension scanner is what finds such a call
site rather than a deprecation log.

What is left is the routing.

- `PageRenderer::addInlineLanguageLabelFile()` is the shape the routing does not
  reach. The manual matches page titles and section paths, never the text of a
  page, so a PHP identifier has no page to be titled after — `inline language
  labels`, `JavaScript labels backend` and `addInlineLanguageLabelFile` at
  `targetVersion: "14"` return no page naming the method, while `Infobox
  ViewHelper state` returns the reference page carrying the deprecation first.

PathUtility::getSystemResourceUri() was filed separately as a hint gap.

## Query

typo3_changelog_lookup {type: "deprecation", version: "14", tag: "ext:backend"} returned Deprecation-108963 addInlineLanguageDomain; the audited code calls PageRenderer::addInlineLanguageLabelFile(), for which no entry exists — settled instead by grepping cms-core/Classes/Page/PageRenderer.php

## Suggestion

What is left of it: say before the manual is called that it has no page for a PHP
identifier. The identifier lookup this asked for is declined in `D-ANS-010`,
which measures what it would have bought.
