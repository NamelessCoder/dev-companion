---
id: D-ANS-069
date: 2026-08-08
status: open
---

# D-ANS-069 — A backlog row carries the review server and not the journal

**A backlog row says whether the review server holds a change, and the comment
count stays behind one read per issue.**

The reporting session narrowed thirty rows to one by reading nine issues whole,
and three of the four it decided cheaply were decided on signals no row carried.
Its own account of why that was affordable is what the measurement below
disproves: the two signals it called free are in the journal, and the
enumeration answer has no journal in it.

## Evidence

- The index endpoint answers no journal. `feedback/2026-08-08-224333` proposes
  carrying "whether the issue has any change on the review server" and "how many
  human comments it has" per row "without a second call", both read off
  `reviews` and `noteCount`, which `Forge::issueOf()` derives from the journal.
  Measured against forge.typo3.org on 2026-08-08:
  `projects/typo3cms-core/issues.json?…&include=journals,relations,attachments`
  answers rows with no `journals` key, and so does the bulk form
  `issues.json?issue_id=82228,83913,81102&status_id=*&include=journals`. The
  comment count is one issue read per row, which on that session's own set is
  36.
- What the same call does answer for free is dropped today. The rows carry
  `relations`, `attachments`, `description` and the project's custom fields, and
  `Forge::entry()` keeps none of the four. Over the feedback's own query — open
  Bugs untouched since 2019-01-01, 36 of them — 19 rows carry a relation and 6
  carry a file.
- The tracker's own settleability field is empty where it would be used. Of
  those 36, `Complexity` is empty on 31 and reads `hard`, `nightmare` or
  `medium` on the other 5. `Sprint Focus` is empty on 35.
- The review server answers a whole page in a handful of calls. The same 36
  numbers, batched twelve to a query as `message:<issue> OR …` with
  `o=CURRENT_COMMIT` and held against the commit message the way
  `Gerrit::names()` already holds a single-issue hit: 3 calls, 36 changes
  returned, 7 rows with a change that really names them. One of the 7 is
  `#82228 → change 53819`, the abandoned change the session spent a `git fetch`
  of the review server to rule out.
- The description is the wrong thing to widen a row with. Median 902 characters
  over those 36, 38.5 kB for the page.

## Decided

- The enumeration answer carries `relations`, `attachments` and whether the
  review server holds a change. The first two cost nothing beyond the call
  already made; the third is one further call per page, which is the same trade
  `D-ANS-064` made for a relation's subject and paid once rather than per row.
- The comment count is not carried. It is one read per row on a host behind bot
  protection, which is the cost the enumeration exists to take off a caller.
- The description stays out. A page of thirty rows is read to choose from, and
  38.5 kB of report text is the reading it was meant to replace.
- `Complexity` is not the answer either, and it is worth saying so once: it is
  the field this question would be asked of, and it is empty on exactly the
  backlog where the question arises.
- What makes a candidate settleable is the skill's to say, and it is research
  rather than a reading of this repository. The row carries signals; the
  criterion that reads them is about how core work actually goes, which this
  judgement has established nothing about.

## Assumed

- Gerrit's `message:` index answers an alternation at page width. Three queries
  of twelve were measured; what bounds it is the URL rather than a documented
  limit, and a limit lower than that would make this one call per few rows
  instead of per page.
- A batched hit means the issue on the same rule a single hit does. The
  commit-message filter `changesForIssue` already applies is what carries over,
  and nothing else about the batched form was verified against a false positive.
- One session, one task shape. The nine issue reads it counted are its own, and
  the criterion it derived is one reader's.

## Wrong if

- A triage reads a row's `reviewed` flag as the verdict and stops, which would
  say the flag needs the same sentence the issue answer carries: a note says
  what was true the day it was written.
- The batched query answers a change for a row whose commit message never names
  it, which would say the filter does not survive the alternation.
- Sessions keep reading candidates whole once the rows carry the signals, which
  would say the reading was never about what the rows were missing.

## Covered by

- `ForgeTest::aRowCarriesWhatTheOneCallAlreadyAnsweredAboutIt`
- `ForgeTest::aRowSaysWhetherTheReviewServerHoldsAChangeThatNamesIt`
- `GerritTest::aPageOfIssuesIsOneQueryAndEachHitLandsOnTheIssueItNames`

## Since then

**Built on 2026-08-09**, and the relations cost a call after all. A relation
carried as a number and a word is what `R-ANS-029` forbids, so the rows go
through the bulk read that already fills an issue's relations — one
`issues.json?issue_id=…` for the whole page, whatever the rows carry between
them, which is the trade `D-ANS-064` made and this entry endorsed for the review
query. So the page is the index call, that fill where any row has a relation,
and one review query per twelve rows.

Recorded live against forge.typo3.org and review.typo3.org the same day, in
`documentation/tools/typo3_forge_lookup.md`: the three oldest open issues came
back with 8 relations, one file and 6 changes between them, `#15984` among them
with the four relations and the three changes `D-ANS-064` was written from.
