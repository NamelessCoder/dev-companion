---
id: D-ANS-094
date: 2026-08-21
status: open
---

# D-ANS-094 — A change answer names the relation chain the change sits in

**`typo3_gerrit_lookup` answers a change with the changes it is stacked on and
the changes stacked on it, told apart from the siblings sharing its Change-Id.**
The head of a fifteen-change stack answers today exactly like a change standing
alone, and nothing in the answer says there is more to read.

## Evidence

- `feedback/2026-08-21-074010` compared this server against another one and read
  change 91563 with `typo3_gerrit_lookup`. Subject, branch, current patch set,
  fetch ref and labels came back in one call and nothing about the stack; the
  session fell back to an unauthenticated `GET` on
  `/changes/91563/revisions/current/related`, which answered all fifteen. That
  one call is what made the feature legible.
- Re-run on 2026-08-21 through `bin/typo3-dev-companion`. `change: "91563"`
  answers one change — `[WIP][FEATURE] Introduce Action API`, NEW on `main` at
  patch set 46, `ad7dc9be…`, Verified and Code-Review — in the text half and in
  the data half alike. The report reproduces.
- The endpoint reproduces with it. `/changes/91563/revisions/current/related`
  answered 200 in 0.12 seconds and 7133 bytes on 2026-08-21, with fifteen
  entries: twelve NEW, 92323 MERGED and 92724 ABANDONED. Which parts of the
  feature landed and which were given up is in that one response and in no
  other.
- An entry carries what the report asks for and nothing has to be fetched per
  change: `project`, `change_id`, `_change_number`, `status`,
  `_revision_number`, `_current_revision_number`, and the commit with its
  subject and its parents.
- The order is git parentage, child first. Entry 0, change 92197, names
  `9ada9785…` as its parent, which is the commit of entry 1, change 92196. The
  named change sits at position 14 of 15, so its place in the list says how much
  is stacked on it and how much under it.
- The two revision numbers per entry are different facts. 92323 participates at
  patch set 8 and stands at 10, measured the same day, so the chain names the
  revision that is in it rather than the change's current one.
- A change with no chain answers `{"changes":[]}` — 20 bytes in 0.07 seconds for
  89011 and for 95169. Nothing in the change payload says beforehand whether the
  call will find anything, which is where this differs from the comments the
  tool fetches only where `total_comment_count` says there are some.
- The handle has to be the change number. `/changes/<Change-Id>/…/related`
  answered 404
  `Multiple changes found for I4b0290760f14296feec6ab30ad49595899ca08f4` on
  2026-08-21 — the backport pair `D-ANS-080` was written about — while 95169 by
  number answered 200.
- Anonymous throughout. No credential was passed on any call above, over the
  host and the REST API `D-ANS-033` already settled, so this costs no second
  host and no secret.
- Nothing here reaches it today. `Gerrit::change()` runs `change:<handle>` and
  one further query for the Change-Id siblings, and no query option carries the
  related changes — the same reason `Gerrit::comments()` has an endpoint of its
  own.
- The corpus does not carry the concept either.
  `bin/cli hints:probe "gerrit relation chain related changes stacked patch series"`
  matched nothing, and
  `knowledge/documents/core/contribution/gerrit-workflow.md` names the parent
  relationship once, in passing, under what `%private` does not hide.
- One session reported it. `bin/cli feedback:list` on 2026-08-21 holds ten open
  feedback and no other names a stack, and nothing in `feedback/archive/` does.

## Decided

- Step 1b, the shape. The answer is available to this server — same host, same
  anonymous API, one call — and there is no argument to `typo3_gerrit_lookup`
  that reaches it. Not step 2: unlike the sibling of `D-ANS-080`, the chain is
  in no response this tool makes today.
- Taken on. What justifies it against a corpus of one is the failure mode rather
  than the count: the answer is not short, it is silently complete. A session
  that does not know the endpoint exists reads the head of a stack, concludes a
  feature is one work-in-progress change, and has nothing to notice by.
- Priority `normal`. Both core skills route a change handle here —
  `typo3-core-patch-review`, `typo3-core-patch-checkout`, and
  `typo3-core-issue-triage` for a change of its own — and that is what takes the
  card off the `low` it arrived at. What keeps it below `high` is that the
  session recovered on its own and lost no task.
- A field on the change entry, not a tool of its own. It is the same subject,
  the same handle and the same moment, and the six verbs give it no name a
  second tool could carry; a caller asking twice for one reading is what
  `D-FBK-020` counts.
- The `change` form alone. An issue search answers up to 25 changes and asks
  whether a patch exists at all, which no stack under one of them changes — the
  rule `REVIEW` and `MESSAGES` already follow.
- Every change the answer carries, siblings included. A backport read alone has
  the same blind spot the report is about, and that loop is the one that already
  fills `comments`.
- The answer says which relation is which. A stack is a sequence of different
  changes built on one another; a shared Change-Id is one patch on several
  branches. The text half already states the second where it applies
  (`D-ANS-080`), and reading the first as the second is the mistake this field
  makes possible.
- Number, status and subject per entry, and which of them the named change is.
  Not the commit and not the fetch ref: the answer already carries those for the
  change under review, and a caller that wants another entry has its number.
- What the field is called and how the text half prints it belongs to the work,
  written against the schema this tool already declares.

## Assumed

- That the chain is worth its call where there is none. Nothing in the change
  payload says whether one exists, so the round trip is paid on every change
  lookup; 0.07 seconds and 20 bytes is one measurement on one day.
- That git parentage is what a caller means by the feature. A stack is a
  relationship between commits rather than a grouping somebody declared, so a
  feature split across changes that do not build on each other is not in it, and
  Gerrit's topic is where that would have been said by hand.
- That number, status and subject are enough to see the shape. It is what the
  report used and what made the fifteen legible to it.
- That the endpoint stays open to an anonymous reader, which is `D-ANS-033`'s
  assumption for everything else this tool reads.

## Wrong if

- A reader takes the status of a chain entry for the state of the change it
  asked about — reads 92323 MERGED and concludes the feature landed.
- A session acts on a chain entry at the patch set the chain names, where the
  change has moved on since; the two numbers are one apart on a merged entry
  already.
- The chain comes back empty often enough that the call buys nothing, which is
  `D-FBK-027`'s own **Wrong if** on the change path.
- A long chain makes a review lookup longer than the review it serves, so that
  the list has to be bounded where nothing bounds it here.
- The topic turns out to be how the core groups a feature, which would mean
  parentage answers a neighbouring question rather than the one that was asked.
- A session that receives the Change-Id siblings and the chain in one answer
  reads them as one set, which would say the label was the whole of the work and
  the two relations should never have shared a paragraph.

## Covered by

- `GerritTest::aChangeCarriesTheStackOfChangesItIsOnePartOf`
- `GerritTest::aChainEntryNamesThePatchSetInTheStackAndTheOneItStandsAt`
- `GerritTest::theChainIsAskedByTheChangeNumberOfEveryChangeInTheAnswer`
- `GerritTest::aChangeStandingAloneHasAnEmptyChainRatherThanNone`
- `GerritTest::aChainCallThatDidNotAnswerIsNotAChangeStandingAlone`
- `GerritTest::theTextHalfSaysWhereInTheStackTheChangeSits`
- `GerritTest::theTwoRelationsAChangeStandsInAreToldApart`

## Since then

Built on 2026-08-21. `chain` is a field of each change entry, filled from
`/changes/<number>/revisions/current/related` by `Gerrit::chain()` — one call
per change the answer carries, by the number, because the Change-Id form still
answers 404 `Multiple changes found` for the pair `D-ANS-080` puts in the same
answer. Every response shape the **Evidence** was measured from reproduced on
the day it was built.

Empty and null are two answers, the shape `comments` beside it already has. `[]`
is a change standing alone and is the ordinary case; `null` is a chain nobody
read, which is every hit of an issue search and a call that did not come back.
An empty list on a failed call would be this side inventing the answer, and
nothing in the change payload could correct it.

The patch set is carried, and as two numbers rather than one. `chainedAt` is
what the stack holds and `patchSet` is what that change stands at now, which is
the only place either fact is available: the chain names a revision no other
response does, and the entry's current one is in the same payload and costs
nothing. The one number on its own is the second **Wrong if** written as a field
— a reader handed `8` for change 92323 and nothing else acts on a patch set two
behind. The text half prints the pair only where they differ, so the ordinary
entry stays one line.

What is not carried is the commit and the fetch ref, as decided, and the entry's
Change-Id with them. The chain relates commits, so an id there would put the
relation this answer is about beside the one it is being told apart from, on the
same entry.

The recorded answer is the report's own change. `bin/cli tools:record` on
2026-08-21 answered `change: "91563"` with all fifteen entries, 92323 MERGED at
patch set 8 against the 10 it stands at, 92724 ABANDONED, and 91563 marked as
the change that was asked about, thirteen from the top of the stack. The two
changes recorded beside it — the backport pair 89011 and 89012 — both answer
`[]`, which is what a chain reads as on a change nobody built on.

The skills are unchanged. `typo3-core-patch-review` and
`typo3-core-patch-checkout` name what the lookup answers in their own words, and
what a chain is is stated by the answer itself.
