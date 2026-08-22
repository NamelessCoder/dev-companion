---
id: D-ANS-054
title: 'The backlog is a third way into the tracker'
date: 2026-08-05
status: confirmed
coveredBy:
  - ForgeTest::theEnumerationAsksForTheOpenIssuesAndReadsThemAsFields
  - ForgeTest::anAreaIsNamedInTheCallersWordsAndMatchedAtAWordBoundary
  - ForgeTest::awordThatNamesNoAreaReadsNothingAndSaysWhichAreasExist
  - ForgeTest::theAreasAreReadFromTheProjectAndHeldRatherThanCopied
  - ForgeTest::theCountOfEverythingThatMatchedComesBackWithThePage
  - ToolContractTest::anArgumentInAnAlternativeNamesTheOnesItExcludes
---

# D-ANS-054 — The backlog is a third way into the tracker

**`typo3_forge_lookup` answers the core project's open issues as a filtered
enumeration, and resolves the area a caller names against the project's own
categories.**

The two ways in that existed both start from something the caller already holds:
a number, or the words a report was written in. A triage holds neither. The
issue nobody has looked at since 2015 is found by no number, because nobody
holds it, and by no wording, because its wording is the one nobody thought of.
That question was unanswerable through this server and cost the caller a hand
written Redmine URL, which is the shape `D-FBK-027` names as earning a tool.

## Evidence

- Measured against forge.typo3.org on 2026-08-05: 2487 issues stand open on the
  core project, the oldest filed 2004-08-20. `/issues.json` takes `status_id`,
  `tracker_id`, `category_id`, `created_on`, `updated_on`, `sort` and `limit` in
  one request and answers `total_count` beside the page.
- `/search.json`, which the wording route uses, takes none of those and cannot
  order by age at all. So the third way in is a different endpoint rather than a
  parameter on the second.
- The core files its issues under 55 categories, read from
  `/projects/typo3cms-core.json?include=issue_categories` the same day.
  `/projects/typo3cms-core/issue_categories.json` answers an empty list without
  a credential, so the project is the way to reach them.
- Nobody types "RTE (rtehtmlarea + ckeditor)". The three questions this was
  written against were worded "rte", "backend ui" and "are there known bugs in
  the RTE".
- A substring match on "rte" reaches "Reporter" and "Renderer".
  `Text::containsWord` is what the rest of this server matches prose with, and
  it separates them.

## Decided

- One tool, three ways in, one record shape. `open` is a third `oneOf` branch
  beside `issue` and `query`, because the entries a caller reads back are the
  same identity in all three and `AGENTS.md` gives two tools sharing an output
  schema one verb.
- The record gained `category`, `assignedTo`, `createdOn` and `updatedOn`. Who
  holds an issue is what says whether it is free to take; the two dates are the
  two different questions age is asked as. A search hit answers the four empty,
  because a hit is a title and the fields are not in it.
- `total` comes back with every page. A caller shown thirty of 2487 that reads
  them as the set has measured the limit rather than the backlog.
- The categories are fetched and held for a day, not written into the source. A
  list in the code is one the core can add to without anything reporting it, and
  the addition is exactly the subsystem somebody would be filtering for.
- A word matching no category reads nothing and says which areas exist. Sent on
  unfiltered it would answer with the whole backlog, which is a set about
  everything wearing the shape of a set about one thing.
- The tracker ids **are** written into the source, against the same argument.
  There are eleven, `/trackers.json` is a second round trip on every
  enumeration, and a twelfth tracker being unfilterable costs one filter while a
  missing category costs a wrong answer.

## Assumed

- The category names the core uses are recognisable in the words a caller
  brings. "backend ui" reaching four areas including "Language Manager
  (backend)" is the fallback working as designed and is answered back, not
  hidden.
- Enumerating by age is a triage's entry point. If the question that actually
  gets asked is "what is worth fixing", age is a weak proxy and the ordering
  earns nothing.

## Wrong if

- Callers reach the enumeration and then ask the same thing again in other
  words, which would say the filters do not carry the question.
- The category fallback is reported as wrong more often than it is used: a word
  selecting four areas where one was meant, in answers people act on.
- The `total` is read as a promise that the rest is reachable, and callers page
  by raising `limit` instead of narrowing.
- The core adds a tracker and somebody has to release this server to filter by
  it, which would say the two lists were split on the wrong side.

## Confirmed on 2026-08-05

The question this was written for was asked for real an hour before it landed. A
session was told by `typo3-core-issue-triage` to call this tool with `open`,
narrowed by `category` and `tracker`, and the schema had none of it
(`feedback/2026-08-05-033805`); it left the server and wrote the Redmine URL by
hand, then spent three round trips guessing at the category id because
`/issue_categories.json` answers 401 without a credential. That is the shape
this entry describes, reported independently of it.

Re-run today with the enumeration in place: `open: "oldest"`, `category: "rte"`,
`tracker: "Bug"` answers 5 of 23, oldest first, with the area resolved to "RTE
(rtehtmlarea + ckeditor)" and both dates on every row — which is the call that
session could not make.

## Since then

Two of its **Decided** bullets have moved, and neither statement above them has.

A search hit no longer answers the four fields empty. What that reads as to a
caller was the next thing reported, and
[`D-ANS-056`](ans-056-a-search-hit-is-filled-from-the-issue-it-is.md) is where
that was decided; the reasoning here — a hit is a title, and the fields are not
in it — is why it takes a further call rather than a wider parse.

The same feedback found that RTE reports sit under
System/Bootstrap/Configuration and under Link Handling as well as under RTE, so
an area filter is a floor rather than the set. The enumeration says that in its
answer where a category was used, which the "the areas that exist come back"
bullet did not cover: a word that matched is as much of a partial answer as a
word that did not.
