---
id: D-ANS-110
title: 'A changelog miss with no re-query names the manual and the rules'
date: 2026-08-26
status: open
coveredBy:
  - PackageSourcesTest::aMissWithNoRequeryToOfferNamesBothCorporaThatAnswer
---

# D-ANS-110 — A changelog miss with no re-query names the manual and the rules

**A changelog miss that offers no re-query names both corpora that answer
instead: the manual for what still holds, the rules for what a patch owes.**

`R-ANS-018` was held over the offering branch alone, which is the branch a
caller least needs it on. The miss with nothing left to ask is the one that ends
the session.

## Evidence

- `feedback/2026-08-24-183536`, re-run on 2026-08-26 from this checkout against
  `.checkouts/main`, which ships 15.0, through `bin/typo3-dev-companion` over
  JSON-RPC. `typo3_changelog_lookup` with `query: "visibility public"` at
  `limit: 25` returns three lines: the miss, the two per-word counts, and what
  the installation ships. No tool is named in either half.
- The branch is most of the short queries rather than an edge of them.
  `Subsets::largestReaching` skips a carried set of one and one the size of the
  query, so no entry can produce a subset from two words at all — every two-word
  miss lands here, whatever the changelog holds.
- The manual does not answer the reported question. `typo3_documentation_lookup`
  at `targetVersion: "14"` with
  `["changelog entry visibility", "method visibility protected public"]` returns
  the Backend entry point page at 44% of the query's weight and says so itself:
  "These pages carry words of the question rather than its subject."
- The rules do answer it. `typo3_rule_lookup` with
  `documentId: "core/contribution/changelog"` returns the document whole, 5990
  characters, opening on `Which Change Owes a Changelog File` — which change
  types owe an entry and which owe none.
- The two shapes are not told apart by anything the answer holds. Both words of
  the reported query exist in the corpus — "visibility" reaches 8 entries,
  "public" 34 — and a subject query that misses reaches the same branch: a
  one-word `BackendLayout` miss is `D-ANS-010`'s shape and lands here too.

## Decided

- **Both tools, on this branch alone.** Nothing in a miss says which of the two
  shapes the question had, and a caller with no re-query left cannot recover
  from being sent to the wrong one. Where a re-query is in hand the corpus
  sentence is about the call after next, so `D-ANS-043`'s single route stays as
  it is and no offering miss grows by a word.
- **The rule route is the writing direction, and says so.** "Whether a core
  patch of your own owes an entry" is the phrasing the tool's own description
  already routes to `typo3_rule_lookup`, and it self-selects: a site developer
  reading it is not writing a core patch and skips the clause.
- **Withheld where a filter or a tag emptied the answer.** Both name their own
  way back into this corpus — ask again without the filter, or replace the tag —
  and routing out of it ahead of a re-query that answers is what `D-ANS-043`
  declined.
- **The per-word counts are not a re-query.** They name one word out of the
  question, and following one discards the question; the subsets are the offer
  because they are the question minus a word. The reported miss printed the
  counts and ended there.
- **Text and no field.** `R-ANS-018` is held by text on both its holders, and
  what `D-ANS-043` put into the data half is what the call computed. This
  sentence is the same on every miss of this branch, so a field carries nothing
  a caller could branch on.

## Assumed

- That a caller reading the miss follows a route it did not ask for. That is
  `D-ANS-043`'s third **Assumed** and this entry inherits it whole.
- That the rule corpus answers the obligation question for the change the caller
  has in hand. It answers the reported one, and `D-KNW-123` is why; a change
  type the document does not place would send the caller back.

## Wrong if

- A later feedback reads this sentence and goes to the wrong one of the two
  tools. Then the miss cannot name both and the branch needs something that
  tells the shapes apart, which nothing in the answer does today.
- A feedback reports the rule clause as noise from a project or extension
  installation. Then the self-selecting phrasing is not enough and the clause
  belongs behind a core checkout rather than beside the manual.
- A two-word miss turns out to be reachable by a subset after all, because
  `Subsets` stops skipping the full-size carried set. Then this branch is an
  edge rather than most of the short queries, and the sentence is being paid for
  on misses that had a re-query all along.
