# State how an extension adds a field to a core palette

**Serves:** feedback/2026-08-18-113327-how-a-core-tt-content-palette-showitem-differs.md, D-KNW-103
**Priority:** normal
**Branch:** todo/how-a-core-tt-content-palette-showitem-differs
**Claimed:** 2026-08-18

Step 1a on `D-KNW-103`: the probe reaches `content-elements` and
`tca-formengine`, the word `palette` is in no hint file, and nothing says that a
core palette's `showitem` is core's string to rewrite. Establish against
`.checkouts/12.4`, `13.4`, `14.3` and `main` what `addFieldsToPalette()` does to
an existing palette and to an appended `--linebreak--`, and how far the
reshaping of the `14` palettes goes beyond `tt_content`'s `frames`, then write
that as a statement where a caller extending a core table arrives — `tca.json`
beside `tca-formengine`, or `content-elements.json` where the `tt_content` words
already lead — with an `appliesTo` that carries `palette`, `showitem` and a
field that is missing from the backend form, and a `HintsTest` case that reaches
it. Bind it only where the reading says it does not hold on all four, and leave
the version question to `typo3_changelog_lookup`, which returns #107789 for
`query: "showitem"` in one call.
