# Say in the brief which hints it left behind

**Serves:** feedback/2026-08-03-144410-task-review-core-commit-9f6c6eb9093-110359-and.md, R-GUI-012
**Priority:** normal

Judged against
[`D-GUI-007`](../../decisions/guides/gui-007-the-brief-carries-a-selection-of-the-hints-and-says-whose-they-are.md),
which decided the count and not the subjects: the sentence says a brief carries
the strongest four per group and points at `typo3_hint_lookup` for the rest,
while the rest has no name until that call is made. `TaskGuide::answer()`
already matches per group through
`Hints::find($group['paths'], $task, self::HINTS_PER_GROUP, null, $targets)`.
Match each group a second time at the lookup's own ceiling of ten, take the ids
the slice dropped, and name them where `HINTS_SOURCE` is printed and in a field
of its own beside `hints` in the payload — an addition, because `AGENTS.md` asks
for fields to be added rather than renamed, and because `outputSchema()`
requires a field on every path through the tool including the miss. `HintLookup`
already answers `availableHints` and `withheldCategories`, so the wording and
the record shape come from there and from `Result/Schema` rather than from a
second model. Hold it with a test beside
`HintsTest::theHintsABriefCarriesNameTheLookupTheyCameFrom`, on the call
`D-GUI-007` was measured from: the five paths of core commit `9f6c6eb9093`,
where the four carried are `fluid-viewhelpers`, `system-extension-boundaries`,
`core-tests` and `fal-basics`, and the three left are `fal-reading`,
`fal-processing` and `dependency-injection`. Then `bin/cli tools:index` and
`bin/cli tools:record`, and `R-GUI-012` moves from `open` to `held`.
