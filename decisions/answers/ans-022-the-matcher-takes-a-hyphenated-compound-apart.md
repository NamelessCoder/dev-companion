---
id: D-ANS-022
title: The matcher takes a hyphenated compound apart, measured over the corpus first
date: 2026-08-02
status: open
---

# D-ANS-022 — The matcher takes a hyphenated compound apart, measured over the corpus first

**A query writing `content-element` reaches nothing that `content element`
reaches, and both halves of the hint match fail on the hyphen alone.**

The spelling is not exotic. It is how the reporting session wrote its own task
down, and it is how this repository spells the id of the hint it was looking
for.

## Evidence

- One measurement, one character apart.
  `bin/cli hints:probe "show assigned related groups in a backend content element preview template"`
  returns `content-elements` at `appliesTo(15) + text(181)`. The same sentence
  with `content-element` matches nothing and returns 40 hints as the index.
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
- It is general, and the shorter compounds fail the other way. `site-package`
  becomes `site-p` and `fluid-templates` becomes `fluid-`; those stems keep the
  hyphen, so they reach only text hyphenated the same way. Asked as a query,
  `content-elements` returns `extension-files` and `sitepackage-layout` ahead of
  the hint of that name, all three at
  `text only(52)`.
- The separators are kept on purpose and the rule is not that they should go.
  `mod.web_layout`, `list_type` and `tt_content` are one token each, and
  `tt_content preview template` reaches `content-elements` at
  `appliesTo(10) + text(129)` because of it. What has no owner is the compound a
  caller hyphenates where the corpus spells it apart.

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

## Since then

The measurement was run, and it found a third gate this entry did not name. The
domain keywords are spaced compounds too, and they are read before anything is
scored: `dark-mode` detected no domain where `dark mode` detects `css`, so every
Backend CSS hint was out of the candidates rather than out of the ranking. That
is upstream of both halves above, and neither of them reaches it.

What was measured, in the two corpora the repository has to hand. The 41
scenario prompts and 66 hint titles, as they stand — eight of them carry an
internal hyphen at all. And the 195 multi-word bare patterns of the hint
`appliesTo`, each asked twice, once as written and once with its spaces
hyphenated: that is what a caller hyphenating a compound the corpus spells apart
looks like, over the whole curated vocabulary rather than over one feedback.
Asked spaced, 176 of the 195 reach the hint the pattern belongs to; asked
hyphenated, 110 did.

Four rules were measured against that:

- The term half alone — `TermSearch::terms()` splitting a hyphenated word into
  its parts. 165 of 195, and three of the hyphenated queries lose every hit they
  had, `content-area` among them.
- The keyword half alone — a bare `appliesTo` phrase matching a hyphen where it
  has a space. 166 of 195.
- Both together, which is what this entry queued. 166 of 195 — the term half
  reaches not one hint the keyword half does not, and carries its three losses
  into the pair.
- The rule in `Text::containsWord()` instead, where a needle of several words
  matches them joined by a hyphen as well as by a space. 176 of 195 — the same
  number the spaced spelling reaches — because that one method is what all three
  gates read: the domain keywords, the `appliesTo` patterns through
  `TermSearch::carries()`, and the frontend and layout markers.

The last is what was changed. Nothing spaced moved under it: the 41 prompts, the
66 titles and the 195 patterns asked as written return exactly what they
returned before, `bin/cli hints:coverage` prints the same page byte for byte,
and `tt_content`, `list_type` and `mod.web_layout` are untouched — only the
space between two words is loosened, and a separator inside one word is left as
`D-ANS-006` has it. The sentence this started from returns `content-elements` at
`appliesTo(15)`, the same way in as the spaced spelling.

So the **Wrong if** above did not happen, and the corpus side it points at is
not the cheaper half after all: it would have meant the hyphenated spelling of
195 patterns, kept in step with every one written afterwards, and it would not
have reached the domain gate at all. The term half was rejected on the
measurement rather than on the risk — it costs three answers and buys nothing
the phrase rule does not already buy. What it would have fixed is the ranking
below the first hit, where the hyphenated and the spaced spelling still differ
in 140 of the 195: `content-element` stems to `conten` and `element` never
enters the query. Nothing measured says that is worth three answers, and this is
the entry to reopen if a query is found that it costs.

The 19 patterns that reach nothing even spaced are not this entry's. They carry
no domain signal — `focus order`, `stacking context`, `utility class` — and the
gate that drops them is the one `everyHintIsReachedByItsOwnTitle` holds for
titles and nothing holds for patterns.

`HintsTest::aCompoundIsFoundWhicheverWayTheCallerJoinsIt` is what would catch
this coming back, and it holds the identifier side as well.
