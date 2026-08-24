# No hint covers how EXT:impexp writes records on import; hint_lookup answers with test and distrib...

**Serves:** feedback/2026-08-24-110812-no-hint-covers-how-ext-impexp-writes-records-on.md
**Priority:** normal
**Branch:** todo/no-hint-covers-how-ext-impexp-writes-records-on
**Claimed:** 2026-08-24

Judged as step 1a on 2026-08-24 and taken on as a hint of its own: `D-KNW-108`
carries the evidence and the boundary, and the writing is what is left. Write
the hint into `knowledge/hints/distribution.json` beside `impexp-artifact`, for
the question "why did the imported records land there" rather than for how the
artifact is written — what decides a page's pid on import: `header.pagetree`
read as a tree by the import and appended to as a flat marker set by the export,
`flatInversePageTree()` reversing each level and handing the top level `-1`, the
parent resolved through `importNewIdPids` as pages are added so the write order
decides whether it resolves at all, `addSingle()` setting a pid only for the
records it creates, and `writePagesOrder()` as the update-mode correction pass
that skips everything carrying `-1`. The five hold unbound on all four covered
majors and `D-KNW-108` names the lines, so no re-reading is owed; what is still
open is what `impexp:import --update-records` sets, which is the option the
`addSingle()` rule turns on and which nothing below `knowledge/` mentions — read
`ImportCommand` in `.checkouts/`, and `typo3_documentation_lookup` for what the
EXT:impexp manual says about it. Name the methods and not the line numbers, give
it an `appliesTo` that a `Classes/Import.php` path reaches as well as a phrasing
like "imported page on the wrong pid", and run `bin/cli knowledge:format` and
`bin/cli hints:probe` with the feedback's own query afterwards.
