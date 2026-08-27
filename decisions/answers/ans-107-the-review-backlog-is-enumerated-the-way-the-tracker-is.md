---
id: D-ANS-107
title: The review backlog is enumerated the way the tracker is
date: 2026-08-25
status: confirmed
coveredBy:
  - GerritTest::aBlockingVoteIsWhatALabelStandsAtWhereTheRulesDisagree
  - GerritTest::aPageOfTheBacklogSaysHowMuchOfItThatIs
  - GerritTest::aPersonOnEitherSideIsOneQuery
  - GerritTest::aSearchAnswersWhatALabelStandsAtWithoutTheVotersBehindIt
  - GerritTest::anEmptyBacklogSaysWhatItCannotSeparate
  - GerritTest::everyBacklogFilterIsAnOperatorComposedHere
  - GerritTest::everyRowCarriesTheSizeTheMergeAndTheAgeOfItsChange
  - GerritTest::theChangesTheirAuthorsMarkedUnfinishedAreOutOfEveryEnumeration
  - GerritTest::theEnumerationIsAWayInOfItsOwnAndTheFiltersNarrowIt
  - GerritTest::theLineAPageIsScannedBySaysSizeMergeAndAge
  - GerritTest::theOldestFirstOrderIsTheMatchedSetSortedHere
  - GerritTest::theReadSaysHowManyItCoveredAndWhetherThatIsAllOfThem
---

# D-ANS-107 — The review backlog is enumerated the way the tracker is

**`typo3_gerrit_lookup` enumerates the open review backlog under the filters a
triage names, and every row it answers carries a change's size, vote state and
whether it still merges.**

Two sessions asked it for the backlog on one day, from different tasks, and both
left the server for hand-built curl against the review API. `typo3_forge_lookup`
answers the same question about the tracker, so this is a hole in one tool
rather than a domain nobody has built.

## Evidence

- `feedback/2026-08-24-183253` was told to work off long-sitting reviews that
  are small and nearly voted through. It ran four paginated calls against the
  changes endpoint, scored 859 changes locally on created date, size,
  Code-Review tallies, mergeable and unresolved comments, and says it would
  write the same script again.
- `feedback/2026-08-24-205050` was told to finish almost-ready open reviews. It
  loaded this tool's schema first, never called it, and wrote `owner:`,
  `reviewedby:` and four `label:` predicates by hand.
- `GerritLookup::inputSchema()` takes `issue`, `change`, `commit`, `query`,
  `path`, `open` as a boolean and `limit`. Nothing orders, nothing filters by
  size, vote or person, and `Gerrit::MOST` caps a page at 25 with no offset.
- Every predicate the two sessions wrote by hand answers anonymously, measured
  on 2026-08-25. `delta:<=60` narrows the 855 open core changes to 329, `age:1y`
  to 159, and
  `-is:wip is:mergeable label:Code-Review>=1 -label:Code-Review<=-1 -label:Verified<=-1`
  — the query the second session's whole task rested on — to 74, in 0.1 seconds.
- `owner:` resolves a name as well as an address: `benjamin.kott@outlook.com`
  and `Benjamin Kott` answer the same 45 changes.
- The three fields both reports ask for cost nothing. A bare row already carries
  `insertions`, `deletions`, `mergeable`, `created`, `unresolved_comment_count`
  and `submit_records`, and `Gerrit::change_()` drops all six.
- The per-voter tallies are the one thing that is not free. A page of 500 rows
  is 666 KB as it comes and 1.14 MB with `o=DETAILED_LABELS`.
- Ordering by age cannot be asked of the review server. It answers by last
  activity, offers no created-date predicate and no total count, so oldest-first
  is the matched set ordered here — the whole open backlog is two calls, 1.1 MB
  and half a second.
- `feedback/2026-08-25-105203` wants the mergeable reading from the other
  direction, having established by hand whether the patch set it fetched still
  applies.

## Decided

- Built, and as a further way into `typo3_gerrit_lookup` rather than as a tool
  of its own. That is what `D-ANS-054` decided for the tracker: one subject, one
  verb, one record shape.
- The enumeration is its own argument and `open` keeps its meaning. It is a
  boolean narrowing `query` and `path` today, and a boolean that becomes the
  sibling tool's `"oldest" | "stale"` enum breaks every caller passing `true` —
  `AGENTS.md` adds fields rather than renaming them.
- The filters are arguments this server composes into a query, never a Gerrit
  query passed through. The operators, the quoting and the escaping stay here
  and `query` in the answer says what they produced, which is what `D-ANS-100`
  decided for the words and the path.
- The row widens for every direction rather than for the enumeration alone:
  size, whether it still merges, when it was created, how many comments are
  unresolved, and the label states `submit_records` carries. That moves the
  per-hit boundary `D-ANS-100` set, and it moves it onto fields the server sends
  unasked.
- The per-voter tallies stay out of a search. They are what reading one change
  answers, and they cost 0.9 KB a row.
- A person is a filter here as it is on the tracker (`D-ANS-089`), by `owner:`,
  by `reviewedby:` and by the union of the two.
- The ordering is this server's, over the set the filters matched, and the
  answer says how many rows it read. A caller shown 25 of 855 that reads them as
  the backlog has measured the limit.
- No breakdown in the first build. It is what answers a set too large to page,
  and the filters measured above cut 855 to 74.

## Assumed

- That the anonymous search index keeps answering these predicates. It is what
  the project's own web UI searches with, and a change to it arrives as a
  smaller answer rather than as an error.
- That size, vote state and mergeable are what a reviewer picks a change by.
  Both reports scored on those three and neither was asked what else it would
  have used.
- That a triage of the review backlog is asked often enough to earn the
  maintenance moving here, which is the trade `D-FBK-027` names.

## Wrong if

- Callers reach the enumeration and go to the API by hand anyway, which would
  say the filters do not carry the question. That is `D-ANS-054`'s first **Wrong
  if** asked on this server.
- The ways into this tool stop being one question. That is `D-ANS-100`'s first
  **Wrong if**, and an enumeration that grows its own answer shape is what would
  revoke that entry rather than extend it.
- The widened row is read as the review. `mergeable` is the server's own last
  computation and a vote a patch set dropped is absent rather than zero, so a
  row that looks like a review while carrying none is worse than one that
  carries nothing.
- Ordering the matched set here turns out to mean reading the whole backlog for
  most questions, at 1.1 MB and two calls each.
- The oldest open changes turn out not to be the ones worth reviewing. Of the
  five oldest measured on 2026-08-25, three are over 250 lines and three no
  longer merge, which is the opposite of what the first report was asked for.

## Since then

Built on 2026-08-25. Four things this entry left open were settled in building
it, each measured against `review.typo3.org` the same day.

The argument is `backlog` and it takes the sibling tool's `"oldest" | "stale"`
spelling, so a caller who knows one enumeration knows the other. `open` keeps
its boolean meaning beside it.

`-is:wip` is in every enumeration and is not an argument a caller sets. It is
411 of the 855 open core changes, so an enumeration that keeps them answers
mostly unfinished work nobody offered for review — and the query the answer
states is where a caller reads that it was applied.

The date filter is `before:` rather than the `age:` this entry measured. Both
read the last update, which is the only date the review server indexes, and an
absolute one means the same thing when the query is rerun a month later.

`label:Verified>=1` was measured and not built. It narrowed the almost-ready set
of 74 to 74, because `negativeVotes: false` already drops what a failed pipeline
leaves, so the argument would have been a second name for a filter that is
already there.

The exclusion the second report's whole session rested on — `-owner:` and
`-reviewedby:`, "not mine and I have not voted on it" — was left out here,
because this entry decided three person filters and every one of them selects.
It is `reviewableBy` since the same day, and `D-ANS-109` is where the fourth
filter and its name were settled.

## Confirmed on 2026-08-27

**The report this entry's last evidence bullet names proposed a different test
for the same question, and the data refutes it.** `feedback/2026-08-25-105203`
asks for a field naming the commit a patch set sits on, and whether that commit
is still an ancestor of the target branch, so the answer says "this still
applies" before anything is fetched. It had established that by hand with
`git merge-base --is-ancestor`.

The purpose is `mergeable`, decided here and shipped by `2779ff52` under two
hours after the report was filed. The proposed test is not it. Change 83672 was
measured on 2026-08-27: its patch set 4 sits on `f2cf6ada165`, and
`git merge-base --is-ancestor` calls that commit an ancestor of `main` in
`.checkouts/main`. The review server answers `mergeable: false` for the same
change, and `standing()` prints `no longer merges`. Ancestry would have called
it applicable.

The two tests come apart on the ordinary case. A patch set nobody rebased sits
on a commit the branch has moved past, and such a commit stays an ancestor
whatever landed on top of it. So ancestry answers yes for every change that was
not force-pushed, and the conflicts are inside that yes.

The parent hash is therefore not added. What it carries beyond `mergeable` is
which revision the patch was written against, which is a question about reading
the diff rather than about whether it applies, and no report has asked it.

The strength half of the same report is judged onto
[`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md),
where the boundary and the guard it left are. Re-run on 2026-08-27 through
`bin/typo3-dev-companion`, change 95369 is `MERGED` at patch set 6 where the
report saw 2, with 95418 on 14.3 and 95419 on 13.4 beside it — the backports the
thread it acted on was about. `mergeable` is null on all three, which is what
the review server computes for a change that landed.
