# Match a hint block by the role of a file, not by the extension it sits in

**Serves:** feedback/2026-08-02-144431-because-one-of-the-two-paths-i-passed-was-typo3.md
**Priority:** normal
**Branch:** todo/match-a-hint-block-by-the-role-of-a-file-not-by-the-extension
**Claimed:** 2026-08-03

The `extbase` block in `knowledge/hints/extbase.json` carries the bare word
`extbase` in its `appliesTo`, so every path below `sysext/extbase/` matches it:
a core bugfix passing `Classes/Service/ImageService.php` got the thirteen plugin
hints — registerPlugin, FlexForm, the paginators, cacheHash — as the largest
block of the answer and skipped all of it, while the two lines that bore on the
task competed with them. Decide between dropping the bare token, which leaves
the block reached by `Classes/Controller/`, `Classes/Domain/Model/`,
`Classes/Domain/Repository/`, `plugin` and `flexform` where it belongs, and
splitting a smaller services set out of it; then sweep the other blocks in
`knowledge/hints/` for the same shape, since a bare extension key is what makes
a block reachable by an extension rather than by a subject. Measure before and
after with `bin/cli hints:probe` on both paths from the report, and add a case
asserting that `Classes/Service/ImageService.php` reaches no plugin hint — a
hint that matches too widely is invisible to `bin/cli hints:coverage`, which
counts what nothing reaches and not what everything does.
