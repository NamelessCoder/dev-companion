# Validate several icon identifiers in one call

**Serves:** feedback/2026-08-11-055257-validating-three-known-icon-identifiers-cost.md
**Priority:** normal

Judged as `D-ANS-078`: step 1b, the shape is missing, and the round trips are
ours — the initialize instructions and four skills say to validate every icon
identifier before emitting it, so a change touching three icons pays three
calls. The entry carries the boundary; the schema is what it left open.

`typo3_icon_lookup` gains a list-valued argument beside `query`. Per identifier:
registered or not, with the category and what it is an alias of. No ranking
behind one that hit; a miss keeps its suggestions, because that is the case
where the next step is the answer.

The one open choice, and the reason this is not a spot fix: whether the verdicts
come back in the `icons` list the tool already declares, with a per-entry field
saying which of them is registered, or in a section of their own. One result set
is the recommendation — it is what `Schema::listOf` already renders and what
`ToolAnswersTest` validates — but a client that renders `icons` as matches would
then show a missing identifier as one. Decide it against `src/Result/Schema.php`
and the two clients recorded under `documentation/usage/`, and say which in the
commit.

`Icons::has()` is the whole of the lookup, so the work is the argument, the
answer shape and the text. `IconLookupTest` is where the cases go, one per
identifier that hits, misses, and is not shaped like an identifier at all.
