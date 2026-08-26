---
id: D-ANS-106
title: A commit in a checkout is a handle the review lookup takes
date: 2026-08-25
status: open
coveredBy:
  - GerritTest::aChangeNamesTheBranchesItsReleasesTrailerClaims
  - GerritTest::aCommitFromACheckoutNamesTheChangeItIsAPatchSetOf
  - GerritTest::aMessageThatWasNotReadClaimsNothingRatherThanNoBranches
  - GerritTest::anEmptyAnswerForACommitSaysWhatItCannotSeparate
  - GerritTest::theTextHalfTellsTheTrailerApartFromWhatWasPushed
---

# D-ANS-106 — A commit in a checkout is a handle the review lookup takes

**`typo3_gerrit_lookup` takes a commit hash as a fourth handle, and every change
it answers names the branches its `Releases:` trailer claims.**

A session triaging old issues held commit hashes and had no way to ask about
them. It spent four git calls per commit, six where a backport was involved, and
told the user a fix had reached two branches where the trailer said three.

## Evidence

- The feedback's question was re-asked against `review.typo3.org` on 2026-08-25,
  because the session's own query was git rather than this server.
- `commit:cf227b18e20` answers change 89740, MERGED on `main`, and the commit
  message that comes back with it carries `Releases: main, 13.4, 12.4` — the
  line the session reached last and had already contradicted.
- The Change-Id query `Gerrit::change()` already makes for every named change
  answers all three siblings: 89740 on `main`, 90012 on `13.4` at commit
  `aaec618cf33`, and 90014 on `12.4`. That commit is the backport hash the
  session went to `git log origin/13.4 -S` for.
- So one call carries what the feedback counted as six, which is the measure
  [`D-FBK-027`](../feedback/fbk-027-the-server-builds-what-costs-its-caller-round-trips.md)
  sets.
- The handle is absent rather than undocumented. `change` is a Change-Id or a
  change number, and `change:cc880c67777` answers HTTP 400
  `Invalid change format`, which `Fetch` reads as no answer at all — so the tool
  reports that the review server did not answer, about the one handle a checkout
  hands it. `commit:cc880c67777` answers change 92881.
- The trailer is in the payload already. `o=CURRENT_COMMIT` is asked for in both
  directions, and `Gerrit::trailers()` reads `Resolves:` and `Related:` out of
  that same message and drops the rest of it.
- `bin/cli hints:probe "which TYPO3 releases contain a given fix commit backport"`
  reaches `public-api-surface`, `breaking-without-a-moved-member` and
  `extension-ter-release`. None of them is the subject, and 1a is not the answer
  either: the fact is on the review server rather than missing from the world.
- One session reports it. What weighs beside that count is the correction — the
  session stated the release set wrongly to the user and took it back a turn
  later, which is a cost no round trip carries.

## Decided

- Taken on, step 1b, a missing shape. Not 1a, because nothing about TYPO3 is
  unknown here. Not 2, because no answer carries the fact and there is nothing
  to move. Not 4, because no wording hands a caller a handle the schema refuses.
- `commit` is a fourth way in beside `issue`, `change` and the search, and it
  queries `commit:`. What it answers is what `change` answers: it names one
  change, and everything after that is the read the tool already makes.
- `releases` is a field per change — the branches the commit message's
  `Releases:` trailer names, read from the message the answer already fetches.
  Empty where the message carries no trailer, which is every change outside the
  core project.
- Release versions stay outside the boundary. Gerrit answers branches, and a tag
  needs the release list held against a merge date, which is a second source and
  a second reading.
- The trailer and the siblings are two claims and the answer keeps them apart. A
  trailer says where the author meant the patch to go; a sibling on a branch is
  a patch that is there. That is
  [`D-ANS-073`](ans-073-what-can-take-a-patch-and-where-this-one-goes-are-two-readings.md)
  one level down.
- The tracker half the feedback asks for is not built.
  [`D-ANS-064`](ans-064-an-issue-answer-holds-what-a-triage-needs.md) decided
  that `reviews[]` carries handles because the state is one
  `typo3_gerrit_lookup` call away, and a trailer there costs one review-server
  call per change on every issue read.
- Queued rather than built in this run. It changes `src/` and a declared output
  schema, which
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  leaves on the far side of the line whatever evidence the judging run holds.
- The card carries `normal`, set by the measured calls and the correction
  against the one session that reported both.

## Assumed

- That a session holding a commit hash thinks to ask this server at all. This
  one went to git without weighing anything else, so the handle needs the
  routing that says it is there.
- That `commit:` takes the abbreviated hash a caller pastes out of `git log`.
  Eleven characters answered on 2026-08-25, and nothing says where the floor is.

## Wrong if

- A session is handed the change, its siblings and its trailer and runs
  `git tag --contains` all the same. The branches would then not be what a
  triage needed, and the version half is the answer rather than something beyond
  it.
- A trailer names a branch no sibling targets and a caller reports the patch as
  released there. The two stand side by side for that reason, and a reader
  taking the trailer for the outcome would say the pairing is not enough.
- The handle is added and nothing passes it, which would make this step 3 and
  the routing the lever rather than the schema.

## Since then

Built the same day the card was taken up. `commit` is the fourth way in,
`releases` is a field on every change whose commit message came back, and the
routing that says the handle is there is in the tool description, in the scope's
`routing` and in the triage intent's tools. The `commit:` query and the trailer
were read against `review.typo3.org` again while the tests were written, and the
fixtures in `GerritTest` are that reading: change 89740 at
`cf227b18e205a3720599f07ac98a8747c7008398`, its backports 90012 on `13.4` at
`aaec618cf33` and 90014 on `12.4`, and the `Releases: main, 13.4, 12.4` all
three of them carry.

The issue direction carries the field too, which the decision above neither
asked for nor ruled out: that search already fetches the commit message to hold
what the server matched against what it says, so one rule covers every path —
the trailer is read wherever the message came back, and `null` is a message that
was not. What stays outside is the search by words and by path, which asks for
no message at all.

### 2026-08-26 — the handle answers whether a local branch is safe to delete

`feedback/2026-08-24-195307` describes a cleanup this entry was not written for
and answers whole. Asked to delete the branches that were his, the session could
not execute the instruction: three branches carried a `Change-Id` that resolved
to nothing on the server and five more sat on commits that were no patch set of
their change, so a delete would have been unrecoverable. It established that per
branch, with a `Change-Id` query for `o=ALL_REVISIONS` and a membership test of
the local tip against the revisions map.

That is `commit` with the branch tip, and it is one call. Measured against
`review.typo3.org` on 2026-08-26 on change 95369, whose six patch sets
`git ls-remote` lists: the current revision `205541b5632`, the superseded
`7e9e4726904` of patch set 5 and `dc97d767f82` of patch set 1 each answer the
change, so the handle reaches a patch set nobody is looking at any more — which
is the case a stale local branch is. A commit nothing pushed answers `empty`,
and `indistinguishable` says there that a private or work-in-progress change
looks the same from here. The feedback's own test states that outcome as
"deletion is unrecoverable" flatly, so what the tool answers is the more careful
of the two.

The card the feedback carries is
[`D-ANS-112`](ans-112-a-change-answer-establishes-the-patch-without-a-fetch.md),
and this half of it is routing rather than a schema: nothing points a session at
the handle before a destructive delete. `typo3-core-patch-checkout` says of its
forced branch deletion that git's refusal "is the last moment anything asks
whether it is really disposable", which this handle makes untrue.

The second **Assumed** above holds unchanged. Eleven characters answered again
on 2026-08-26, and where the floor is is still unmeasured.
