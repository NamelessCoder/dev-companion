# nothing warns that widening a public non-final core method signature breaks subclasses

**Serves:** feedback/2026-08-08-224316-nothing-warns-that-widening-a-public-non-final.md, R-KNW-066
**Priority:** normal
**Branch:** todo/nothing-warns-that-widening-a-public-non-final
**Claimed:** 2026-08-08

Judged on 2026-08-09 as step 1a with a step 4 beside it, and queued:
`R-KNW-066` is what must hold, `D-KNW-065` is the evidence and what was rejected
with it.

First step is the reading, across `.checkouts/`: whether the core files an
*added* optional parameter on a public non-final method as breaking without
exception, or only where something outside the core overrides the method, and
which of the additive setter and making the class or the method final it
prefers. That decides whether the hint states a rule or a consideration, and it
decides the one-word correction to `breaking-not-assessed` in
`src/Knowledge/CommitMessage.php` and to `## Breaking Changes` of
`knowledge/documents/core/contribution/commit-messages.md`, which name removing
and narrowing alone.

Then the placement, which `D-KNW-065` left open: a hint of its own against an
existing domain, and what its `appliesTo` may be keyed on without firing into
every core PHP answer. `skills/typo3-core-patch-review/references/checklist.md`
is the third file to look at — it enumerates the public API surface "from the
diff's deletions rather than from its additions", which is where an added
parameter falls out.
