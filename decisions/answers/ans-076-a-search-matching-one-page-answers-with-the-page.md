---
id: D-ANS-076
title: 'A search matching one page answers with the page'
date: 2026-08-11
status: open
coveredBy:
  - KnowledgeTest::aCutAnswerNamesTheHeadingsOfThePageItLeft
  - KnowledgeTest::aSearchWhoseMatchesAreAllInOnePageAnswersWithThePage
  - KnowledgeTest::everyToolThatRendersASectionOffersThePageAsACall
---

# D-ANS-076 — A search matching one page answers with the page

**`typo3_rule_lookup` returns the whole document where every section it matched
is in one, instead of the excerpts and an offer to fetch it.**

Two attempts at telling a session what a cut left of a page are in the server,
and
[`D-ANS-070`](ans-070-a-document-is-handed-over-by-the-call-that-reads-it.md)
records that both were in the session that then searched the same page twice.
What is removed here is the round trip rather than the sentence about it.

## Evidence

- **The reported pair, re-measured on 2026-08-11 at `targetVersion=15.0`.**
  `feedback/2026-08-10-182523` made two calls minutes apart, each matching one
  heading of `core/contribution/commit-messages`: 2940 and 3346 bytes over two
  calls, against 11742 for the page in one. The first answer's foot named
  `Release Targets`, which is what the second search went looking for.
- **A call is the cost and the text is not.**
  [`D-FBK-020`](../feedback/fbk-020-a-session-is-charged-per-call-so-the-calls-are-what-is-budgeted.md)
  measured 718 million cached input tokens read back over 5414 calls against 5.9
  million written out. Two calls at 6286 bytes is the expensive shape and one at
  11742 is the cheap one.
- **How often it fires.** Of the 56 topics the corpus declares as its own
  subjects — every `##` heading of every document, which is what
  `Documents::topics()` answers with — 17 reach exactly one document and 39
  reach several. So roughly a third of queries, and two thirds keep the cut and
  the line under it.
- **What a page costs beside the excerpts.** Median 2.6 times the cut answer,
  worst 6.1, and no query where the page was the shorter of the two. The ceiling
  is the corpus rather than the ratio: ten documents, the largest
  `core/contribution/commit-messages` at 11742 bytes and the median at 4328.
- **The cut is not always even whole.** `Documents::MAX_SECTION_LENGTH` cuts a
  section at 2400 bytes, and the excerpt then says so and names the same second
  call — which `KnowledgeTest::aCutScriptSectionSaysHowToReadThePageWhole` holds
  for the script lookup.

## Decided

- **The condition is one document, not a share of it.** A search whose hits all
  sit in one page has established which page answers the task, and how much of
  it the query happened to name says nothing about what the next question needs.
- **The page as written**, front matter and sections outside the target
  included, which is what `documentId` already returns and for the reason stated
  there: a section left out for a major it does not hold on is a hole in a page,
  and every bound section carries its own range under its heading. One
  behaviour, not two.
- **`matchedHeadings` is added rather than `matches` reshaped.** The caller
  still learns what the query hit, and `matches` carries the one record for the
  page — the shape the `documentId` path already answers in, which is now built
  in one place for both.
- **`typo3_rule_lookup` alone.** `typo3_script_lookup` and `typo3_task_guide`
  render the same corpus through `Prose::sections()` and keep the cut. Nothing
  has reported the round trip there, and `Prose::wholePage()` is one line away
  when something does.
- **The cut answer keeps everything it had.**
  [`R-ANS-028`](../../requirements/answers/ans-028-an-answer-that-names-a-document-says-how-to-read-it-whole.md)
  and the foot line of `D-ANS-070` hold for an answer that really is a cut,
  which is the two thirds of queries reaching more than one page.

## Assumed

- **That a page handed over is read.** Nothing here shows it. What is shown is
  two sessions not acting on a sentence that named the page, which is why the
  sentence is not what was strengthened a third time.
- **That the corpus stays small enough for a page to be an answer.** Ten
  documents on the date above, the largest under 12 kB. Nothing caps a document,
  and `bin/cli tools:record` is where the answer lengths are visible.

## Wrong if

- A session reports an answer of this tool as too long to read, or reports
  skipping one. That is the second **Wrong if** of
  [`D-ANS-061`](ans-061-an-answer-that-names-a-document-hands-it-over.md),
  which nothing could satisfy until now, and it would say the cut was right and
  only its label was wrong.
- A session is handed a page whole and searches into it a second time anyway.
  Then the round trip was never the cut's doing.
- A caller on an LTS acts on a section of a handed-over page that holds on main
  alone. Then the page as written is wrong for a search that carried a target,
  whatever it is right for on `documentId`.
- A document grows to where the page stops being an answer. Then the
  concentrated case needs the ceiling the section cut already has.

## Since then

The mechanism is untouched and which recorded call demonstrates it moved, on
2026-08-18, when `project/installation/booting-a-clone` was added to the corpus
— `D-KNW-095`. A term's weight is computed over the sections in front of the
query, so eight more of them carried two sections of the first call over the
coverage floor, one of them in a second page. The first call is the cut now and
the second is the page, and the round trip is still saved: `Release Targets`,
what the second search went for, is among the three sections the first answer
returns.

This is the fourth **Wrong if** arriving from the other side. What it names is a
document growing until a page stops being an answer; what happened is the corpus
growing until a query stops being concentrated. Neither caps anything yet, and
the count in **How often it fires** is what a further document moves.

A judging run put the premise under **Decided** on 2026-08-24.
`feedback/2026-08-24-110851` searched `signed-off-by` and was handed
`any/testing/proving-a-condition` whole, on one section matching one term — the
corpus carries a "signed in" and nothing about the trailer. "A search whose hits
all sit in one page has established which page answers the task" was measured on
searches with several hits and holds of them; a search with one hit satisfies
the same condition and establishes nothing.
[`D-ANS-101`](ans-101-a-concentrated-search-is-more-than-one-match.md) puts a
floor under the count and leaves the rest of this entry standing.

The floor is in since 2026-08-24, and the statement above holds of a search with
more than one hit. What it costs was measured on the same corpus this entry
counted: of 103 subjects, 25 reach one page and 12 of those reach one section,
so a ninth of them is answered with the section and the offer. The reported pair
is among them — its second call matches `Release Targets` and nothing else — so
this entry's own founding calls are two cuts now, and the first answer naming
what the second went looking for is what saves that round trip.
