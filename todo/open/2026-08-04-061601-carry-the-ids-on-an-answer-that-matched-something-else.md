# Carry the ids on an answer that matched something else, not only on an empty one

**Serves:** feedback/2026-08-04-055626-task-add-a-code-style-fixer-to-a-standalone.md
**Priority:** normal

Step 2 on the ladder, and it reproduces today —
[`D-KNW-055`](../../decisions/knowledge/knw-055-the-first-check-a-standalone-extension-repository-gets-is-a-gap-this-server-owns.md)
has both calls re-run. `MatchedHints` fills `availableHints` on a miss alone, so
the answer that matched three irrelevant hints is the one that withholds the
index a caller would correct itself with, and an `id` lookup shows no neighbours
either. Carry the same index on every answer of `typo3_hint_lookup` — the hints
in the domains that were searched, minus the ones returned — and bring the `id`
parameter's description and the `availableHints` schema line in step with what
it then does, since both say today that the list comes on an empty answer.
Measure before choosing between the whole domain index and the categories the
matched hints are in: `bin/cli hints:coverage` counts 127 hints over the corpus,
and what a `php` query would attach is the number that decides whether the full
list is affordable. Hold it in `tests/Contract/` with a case that a query
matching something still names an id it did not return, and re-run the
feedback's two calls through `bin/typo3-cms-mcp` before archiving it.
