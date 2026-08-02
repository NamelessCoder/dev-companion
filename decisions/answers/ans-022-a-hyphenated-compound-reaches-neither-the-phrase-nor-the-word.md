---
id: D-ANS-022
date: 2026-08-02
status: open
---

# D-ANS-022 — A hyphenated compound reaches neither the phrase nor the word

**A query writing `content-element` reaches nothing that `content element`
reaches, and both halves of the hint match fail on the hyphen alone.**

The spelling is not exotic. It is how the reporting session wrote its own task
down, and it is how this repository spells the id of the hint it was looking
for.

## Evidence

- One measurement, one character apart.
  `bin/cli hints:probe "show assigned related groups in a backend content
  element preview template"` returns `content-elements` at
  `appliesTo(15) + text(181)`. The same sentence with `content-element`
  matches nothing and returns 40 hints as the index.
- The keyword half. `ArchitectureHints::scoreKeywords()` looks for the pattern
  in the query rather than the query in the hint, and a pattern of bare words
  goes through `TermSearch::carries()`, which anchors it at a word boundary. So
  the `appliesTo` pattern `content element` is searched for verbatim and a
  hyphen in the query is a miss.
- The term half. `TermSearch::terms()` splits on `[^\p{L}\p{N}_.-]+`, so a
  hyphen stays inside the word, and `stem()` then cuts anything longer than six
  characters. `content-element` becomes `conten`, which is what `content`
  becomes on its own — `element`, the discriminating term, never enters the
  query at all.
- It is general, and the shorter compounds fail the other way.
  `site-package` becomes `site-p` and `fluid-templates` becomes `fluid-`;
  those stems keep the hyphen, so they reach only text hyphenated the same way.
  Asked as a query, `content-elements` returns `extension-files` and
  `sitepackage-layout` ahead of the hint of that name, all three at
  `text only(52)`.
- The separators are kept on purpose and the rule is not that they should go.
  `mod.web_layout`, `list_type` and `tt_content` are one token each, and
  `tt_content preview template` reaches `content-elements` at
  `appliesTo(10) + text(129)` because of it. What has no owner is the compound
  a caller hyphenates where the corpus spells it apart.

## Decided

- Recorded and queued rather than fixed here. It is `src/`, it is the matcher
  two tools and the whole hint corpus go through, and
  [`D-KNW-009`](../knowledge/knw-009-a-domain-keyword-is-a-phrasing-not-a-word.md)
  is the standing evidence that widening a match trades a miss for a wrong hit
  unless it is measured over the corpus first.
- Not [`D-ANS-006`](ans-006-an-identifier-is-found-however-it-is-spelled.md).
  That rule is in `LabelSearch::carryingEvery()`, it removes separators so an
  identifier is found however it is spelled, and neither the hint terms nor the
  `appliesTo` patterns go through it.
- Not the reason the feedback was filed. The hint the query missed carries no
  answer to it either — that is
  [`D-KNW-014`](../knowledge/knw-014-the-record-variable-a-v14-preview-template-is-handed-is-a-gap-this-server-owns.md).
  This is what the same query would still have cost after that gap is closed.

## Assumed

- A caller hyphenates a compound the corpus writes apart more often than the
  reverse. One feedback is the evidence for it, and the queued step is a
  measurement over the 40 scenario prompts and the hint titles rather than the
  change.

## Wrong if

- The measurement finds the compounds are rare and the widening moves an answer
  that was right. Then the corpus side is the cheaper half: the `appliesTo` of
  the hints that name a compound gains the hyphenated spelling, and the matcher
  is left alone.
- Splitting on the hyphen costs an identifier. `tt_content`, `list_type` and
  `mod.web_layout` are what to re-measure, because a rule that also loosened
  those would trade this miss for the one `D-ANS-006` was written against.
