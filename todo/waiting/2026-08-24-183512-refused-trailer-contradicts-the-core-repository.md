# refused-trailer contradicts the core repository's own AGENTS.md on Signed-off-by

**Serves:** feedback/2026-08-24-183512-refused-trailer-contradicts-the-core-repository.md, feedback/2026-08-25-114636-refused-trailer-calls-signed-off-by-an-error.md
**Priority:** normal
**Waiting on:** whether `typo3_commit_message_guide` strikes a `Signed-off-by:`
    that was already on the patch set. The maintainer ruled on 2026-08-24 that
    the trailer is not **set** on a core patch, and the tool takes it off every
    core message it is handed — so a session amending somebody else's patch set
    is told to remove that person's Developer Certificate of Origin attestation,
    which is a different act. `R-KNW-075` says which trailers a core commit
    carries is settled here and a session that believes one is owed asks rather
    than deriving it, so this is not a reading anybody can make from a checkout.
    The reading behind it is in
    [`D-KNW-110`](../../decisions/knowledge/knw-110-a-core-commit-message-carries-three-trailers-and-the-hooks-change-id.md),
    under the entry of 2026-08-25.

Judged on 2026-08-25 as the ladder's step 4, wording, on `D-KNW-110`, which two
sessions have now met and none of whose **Wrong if** they fire. The card serving
`feedback/2026-08-25-114636` was folded in here rather than judged again: it is
the same check, reported from a fresh commit where this one was reported from an
amend.

What the reading established, so it is not done again. The rule holds and the
level follows from it — the draft this tool returns is committed as it stands,
so a refused trailer left in it would contradict its own check. The four facts
both feedback ask the answer to state are in
`knowledge/documents/core/contribution/commit-messages.md` already, verified and
ruled on: the hook deletes `^Signed-off-by:` and checks for nothing, the
Contribution Guide and `CONTRIBUTING.md` ask for none of it, about one merged
commit in a hundred carries it, and it is struck by a reviewer rather than
rejected by a check. Neither session read that page, and each re-counted the
merged history by hand instead.

The step that does not wait: the `refused-trailer` message in
`src/Knowledge/CommitMessage.php` names the core's `AGENTS.md` as what it
overrules, in place of "whatever the checkout you are working in says", and says
the trailer is struck by a reviewer rather than rejected by a check. That is a
copy from the page above rather than a new statement, and it leaves the level,
the code and the draft alone —
`CommitMessageTest::aCoreDraftRefusesTheTrailersTheProjectDoesNotSet` asserts
the codes and not the prose. It is queued rather than made in the judging run
because it is `src/` (`documentation/records/judging.rst`).

Answered the other way — an existing trailer stays — the same message gains the
case and the parse keeps a sign-off it did not draft, which is where the shape
of the fix changes.
