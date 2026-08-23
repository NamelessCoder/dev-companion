---
id: D-ANS-090
title: 'A matched set larger than a page is answered by its shape'
date: 2026-08-19
status: open
coveredBy:
  - ForgeTest::aBreakdownCountsTheWholeSetRatherThanAPageOfIt
  - ForgeTest::aBreakdownSaysWhereTheBoundCutTheRead
  - ForgeTest::aUnionIsTwoReadsMergedAndCountedWithoutTheIssuesBothCarry
  - ForgeTest::theAreasComeBackOnlyWhereAWordOfTheCallersNeedsCorrecting
  - ForgeTest::theCountedReadPagesUntilTheWholeMatchedSetIsRead
  - ForgeTest::theLargestBucketsAreAnsweredAndTheTailIsCounted
---

# D-ANS-090 — A matched set larger than a page is answered by its shape

**A matched set larger than a page is answered by its shape, a person by both
sides at once, and the area list only where a word needs correcting.**

`typo3_forge_lookup` takes `breakdown` for the first and `involving` for the
second, and answers `categories` only on the call it corrects something on.

All three came out of one session on the day the person filters shipped, and all
three are about a set rather than about a name: how big it is, which two
questions it is, and what an answer carries that nobody asked for.

## Evidence

- `feedback/archive/2026-08-19-134651`: `reportedBy` with `limit=50` answered
  `total=621` and 50 rows, and rows 51 to 621 are reachable by nothing. The
  session reported the count, listed the 4 open ones, and handed the user a
  forge.typo3.org URL — the answer to the literal request came from the web UI.
  Its own reading is that what it needed was the shape of the set and not 621
  rows in its context.
- The tool's defence of the cap — a set that has to be paged through is answered
  by other words — holds for a topic and not for a person. There are no other
  words: `tracker`, `category` and a date each answer a smaller question than
  the one asked.
- `feedback/archive/2026-08-19-134706`: "issues von Frank Nägler" is both sides
  at once, and the tracker ANDs its filters, so the pair answers issues somebody
  filed *and* holds. The session made two calls and merged them by hand; #89326
  is in one set and not the other, and neither answer says the other set exists.
- `feedback/archive/2026-08-19-134717`: three calls, none passing `category`,
  each answered with all 54 area names — roughly 600 tokens repeated three
  times, more payload than the four issues being reported on.
- Measured on 2026-08-19: the 621 issues one person had filed sit in 5 statuses,
  5 trackers, 38 areas and 19 years, and the four largest areas hold two thirds
  of them. Reading them takes 7 requests and 4.3 seconds; the union of both
  sides is 764 and takes 13.
- Redmine answers no counts for a `group_by` over its JSON API, measured the
  same day, so a shape is read rather than asked for.
- The other vocabularies this server echoes already answer only where they do
  work, which the feedback asked be checked: `typo3_schema_lookup` carries its
  table index on the call that named no table and on a name it does not have,
  and `typo3_changelog_lookup` fills its tag list only where a tag was passed.
  The area list was the one that did not.

## Decided

- **`breakdown` answers the counts and no rows.** The session said the 50 rows
  were the one thing it did not need, and the rows are the expensive half of the
  answer. What it costs here is a read per hundred issues, which is the trade
  the caller cannot make on its side at all.
- **The dimensions are status, tracker, area and year filed.** The four the
  feedback named, which are what "what has this person worked on" is asked in.
- **The read is bounded at ten pages and says where the bound cut it.** Ten
  requests is about seven seconds; past that a caller is waiting on a shape it
  should be narrowing instead. `complete: false` and the text both say the
  counts are of one end of the set, because proportions read off the oldest
  thousand of three are a wrong answer with a right shape.
- **Twelve buckets per dimension, and what is left out is counted.** The tail of
  an area count is twenty subsystems holding one issue each. An issue filed
  under no area is a bucket named `none` rather than a row left out, so the
  buckets add up to what was read.
- **No `offset`.** It was the feedback's own second choice and it says why: 621
  rows through a context is the wrong trade, and a dozen calls to get there is
  worse. Paging stays the thing this tool does not do.
- **`involving` is a third argument rather than a mode on the pair.** The pair
  answers two different questions and the description is right that they do;
  what was missing is the broad question, which is the one a user says out loud.
  It takes the place of the two rather than combining with them.
- **A union is two reads, merged, and counted by a third.** Taking the first
  `limit` of each read and the first `limit` of what they merge to is the first
  `limit` of the union, because both come back in the same order. The count is
  the two totals less the issues both carry, which is one more read of one row —
  a caller told 621 and 588 cannot tell what that is together.
- **The area list is answered only where a category word resolved to none or to
  several.** That is the call it does work on. Everywhere else it is vocabulary
  the caller did not ask for, and `typo3_server_scope` is where a caller that
  wants it without a question reads it.
- **The page sentence points at `breakdown` where a person is in the filters.**
  Telling a caller to narrow by tracker or date is the advice that sent the last
  one to the web UI.
- **Nothing is dropped from `people`.** `D-ANS-089`'s **Confirmed on** records
  that an empty `candidates` is what let a session state its result plainly, so
  the block stays whole on every answer that resolves a name.

## Assumed

- That a shape is what most callers want from a large set. One session says so
  about its own task, having tried the other way first.
- That ten pages is the right bound. It is a wait nobody has complained about
  yet, and what is on the other side of it is a caller who should narrow.
- That a union of more than two sides is nobody's question. A watcher, a
  commenter and a reviewer are all on a Redmine issue, and none of them was
  asked for.

## Wrong if

- A session asks for a breakdown and then asks for the rows anyway, twice. Then
  the two are one answer and the counts belong beside a page rather than instead
  of it.
- A session reads proportions off a bounded breakdown as if they were the set's.
  Then `complete: false` is not loud enough and a bounded read should refuse
  instead.
- A session passes `involving` and then needs to know which side a row came in
  on, and the row's own `reportedBy` and `assignedTo` do not tell it. Then the
  union owes each row the reason it is in the set.
- A caller passes no `category`, needs the area names, and has to call
  `typo3_server_scope` for them. Then the echo was doing work nobody had
  measured.
