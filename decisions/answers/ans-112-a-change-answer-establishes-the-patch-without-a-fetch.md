---
id: D-ANS-112
title: A change answer establishes the patch without a fetch
date: 2026-08-26
status: open
coveredBy:
  - GerritTest::aChangeReadByNameCarriesThePathsItsPatchSetTouches
  - GerritTest::aFileWithNoLinesToCountSaysWhyItHasNone
  - GerritTest::aMovedFileNamesThePathItCameFrom
  - GerritTest::pathsNobodyAskedForAreNotPathsThatCouldNotBeRead
  - GerritTest::theCommitMessageIsReadByBothFormsAndHandedBackByAChangeReadByName
  - GerritTest::theTextHalfCarriesTheCommitMessageWhole
  - GerritTest::theTextHalfNamesEveryPathAndWhatThePatchDoesToIt
---

# D-ANS-112 — A change answer establishes the patch without a fetch

**`typo3_gerrit_lookup` answers the paths a patch set touches and the commit
message it carries, so a change is established without being put on disk.**

The answer says how to fetch a patch set and nothing about what is in one, so
the ref is the only route this server offers to either fact. A session triaging
a shortlist fetched every change on it into the user's own working checkout.

## Evidence

- `feedback/2026-08-24-195307` fetched eight open changes into the user's
  working checkout to triage them and tagged each one `gr/<number>`, then a
  ninth for the review itself. The user stopped the session over it twice. Its
  own account is that the review never needed the refs, and that
  `/changes/<n>/revisions/current/{commit,files}` would have covered the
  shortlist.
- `feedback/2026-08-25-105203` is a different task shape reaching the same
  endpoint by hand. Before the skill ran, that session had already called
  `changes/95369/detail?o=CURRENT_REVISION&o=CURRENT_COMMIT` with raw curl.
- Re-run on 2026-08-26 against the review server. `change: "95369"` answers
  number, changeId, subject, status, branch, patchSet, commit, project, updated,
  created, insertions, deletions, mergeable, url, fetch, labels, commentCount,
  unresolvedCommentCount, comments, chain, issues, releases and messages. It
  names no file and carries no commit message, and `+47 -48` is the whole of
  what it says about the diff.
- The message is fetched already and dropped on purpose. `o=CURRENT_COMMIT` is
  asked for on both queries that read it, and `Gerrit::changesForIssue()` and
  `Gerrit::issues()` each `unset()` it before answering, because what those two
  read it for is the handles.
- `typo3-core-patch-review` asks for the changed paths as one of the four things
  a review establishes, and passes them to `typo3_hint_lookup`,
  `typo3_test_run_guide` and its own enumeration of what the diff removes. It
  asks for the commit message as the argument of `typo3_commit_message_guide`.
  The fetch is the only way this server offers to reach either of them.
- `bin/cli hints:probe "reviewing open gerrit changes without fetching refs into the checkout"`
  matched nothing. This is not 1a either: the fact is on the review server
  rather than missing from the world.

## Decided

- Taken on, step 1b, a missing shape. Not 2, because no answer carries the paths
  and there is nothing to move. Not 4, because no wording could tell a review to
  work from the API while the API answer withholds what the review is told to
  establish.
- `files` is the paths the current patch set touches, which is Gerrit's
  `o=CURRENT_FILES` on the query already being made rather than a second call.
- `message` is the commit message that is fetched today and dropped. What made
  dropping it right is that nothing then read it; the review skill reads it, and
  a caller holding the subject alone cannot check a trailer.
- The boundary is what the patch touches and what it says about itself, and not
  the diff. A file list decides a triage, the hunks are what a fetch is for, and
  that is the line the reporting session itself drew.
- Both fields go on a change read by name, where the votes and the comments
  already are —
  [`D-ANS-079`](ans-079-a-change-answer-carries-its-votes-and-its-comments.md).
  A search answers up to 25 changes and asks whether a patch exists at all,
  which no file list changes.
- [`D-ANS-068`](ans-068-a-change-answer-carries-the-ref-that-fetches-the-patch-set.md)
  stands. The ref is still the answer where a checkout is genuinely needed, and
  it was the whole answer only because nothing beside it said what the patch
  was.
- Queued rather than built in this run. It changes `src/` and a declared output
  schema, which
  [`D-FBK-052`](../feedback/fbk-052-a-judgement-that-holds-the-evidence-makes-the-change.md)
  leaves on the far side of the line whatever evidence the judging run holds.
- Nothing holds it yet, so `coveredBy` is empty until the change lands with the
  tests that read the two fields.
- The card carries `normal`. Two sessions from different task shapes reached the
  same endpoints by hand, and one of them cost its user the session.

## Assumed

- That `o=CURRENT_FILES` serves the file list over the same anonymous path as
  the options already asked for. It is Gerrit's documented option for it and was
  not measured here.
- That one file list is small enough to sit on every change a lookup by name
  answers. A core patch is one commit, so this is the size of a patch rather
  than of a series, and it was not weighed.

## Wrong if

- A session holds the file list and fetches the change anyway, which would say
  the paths were not what sent it to the checkout.
- The file list is the weight that makes a change lookup too big to read, which
  would put it behind a parameter rather than in the answer.
- A triage acts on a file list where the hunks contradict it — a path the patch
  only moves, read as a path it rewrites — which would say the boundary is drawn
  in the wrong place.

## Since then

Built on 2026-08-26. `files` and `message` are on a change read by name, which
is the `change` handle and the `commit` handle alike, and `null` everywhere
else. The key is present and null rather than absent, because that is what the
fields beside it already do and a caller branching on whether a key is there
cannot tell an answer that read nothing from a server that carries nothing.

Both **Assumed** above were measured against `review.typo3.org` that day.
`o=CURRENT_FILES` serves the list over the same anonymous path as the options
already asked for: change 95369 answers 19.8 KB without it and 23.8 KB with it,
for the 25 files it touches. The weight was read off the population rather than
off that one change — of 200 open core changes read the same day, the median
touches 5 files, the ninetieth percentile 40 and the largest 233. So the list
rides on every named change rather than behind a parameter, and what a lookup
grows by is regularly under a kilobyte.

What a file entry carries came out of the same reading. A path the patch only
edits carries no `status` at all, so `modified` is the absence; `A`, `D`, `R`,
`C` and `W` are the others, and the answer spells them out because a letter is
unreadable where a path is not and `status` is already `NEW` and `MERGED` one
level up. Gerrit omits a line count that is zero and both of them on a binary,
which is why `binary` stands beside the two zeros: change 95385 adds two `.png`
fixtures that would otherwise read as files nobody touched. `old_path` is what
separates a rename from a delete and an add, and change 95211 moves 38 files out
of one system extension with it.

The two skills carry the other half, which is what made this more than a schema
change. `typo3-core-patch-review` establishes the four things from the lookup
rather than from "one reading of the diff", and the review server is the first
tool it routes to; where the work needs the patch on disk it crosses into
`typo3-core-patch-checkout` for that one change and names the ref in the report.
That skill opens on the same premise from the other side: a shortlist is triaged
before it is opened at all.

`D-ANS-106`'s dated section asked for one more piece of routing and it is in the
same skill: the forced deletion of a review branch names `typo3_gerrit_lookup`
with `commit` before it runs, so what answers whether the commit is recoverable
is the lookup rather than git's refusal.
