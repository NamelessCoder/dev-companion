# Give the gerrit answer the ref that fetches the patch set it names

**Serves:** feedback/2026-08-08-224354-typo3-gerrit-lookup-returns-the-patch-set-but.md
**Priority:** normal

Judged on 2026-08-09 as `D-ANS-068`: step 2, delivery. The ref form is in
`knowledge/documents/core/contribution/gerrit-workflow.md`, it reached neither
session that needed it, and every input to it is already in the answer.

Next: `GerritLookup` gains a field per change entry carrying
`refs/changes/<last two digits>/<number>/<patch set>` and the review server URL
it is fetchable over, null where `patchSet` is `0`. The `outputSchema()` gains
it, `tests/Contract/` holds it on a hit and on a miss, and `bin/cli tools:index`
is rerun where the description moves. Two things are unmeasured and belong to
the step: what a change numbered below ten shards to, and whether the sharding
is Gerrit's documented rule rather than a property of this instance.

`feedback/2026-08-08-224352` reports the same shape from a triage. This card
serves only the feedback named above.
