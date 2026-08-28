---
id: D-KNW-073
title: The corpus states what makes a change breaking with no member moved
date: 2026-08-14
status: open
coveredBy:
  - HintsTest::aChangedRenderingIsAnsweredAsTheBreakingMoveWithNoMember
  - HintsTest::theQuestionWhetherAnEntryIsOwedReachesTheTestThatDecidesIt
---

# D-KNW-073 — The corpus states what makes a change breaking with no member moved

**A change that moves no PHP member is breaking on what it renders, and the
target branch decides between `Breaking` and `Important` where the effect is the
same.**

`breaking-without-a-moved-member` carries both halves, beside
`public-api-surface` rather than inside it: one hint is one question, and the
two questions are a member that moved and a member that did not.

## Evidence

- The sweep is
  [`D-KNW-072`](knw-072-what-makes-a-change-breaking-without-a-member-moving-is-a-subject-this-server-owns.md)'s
  **Confirmed on**, read across the four covered checkouts on the same day. What
  it settled is that the two entries the card started from are the rule, that
  `Howto.rst` says "may break or **affect** third-party code", and that no
  maintained line's `<lts>.x` directory has carried a Breaking entry since
  `8.7.x`.
- The two queries the reporting feedback wrote reached nothing before and reach
  this hint alone now — `bin/cli hints:probe` on 2026-08-14, where the first
  matches on the hint's own words and the second on `appliesTo` as well.
- The reviewer's own question reaches it first. Probed with the patch the
  session was reviewing — `lib.parseFunc_RTE`, a caption gaining a `<p>`, a
  backport to a maintained line — the new hint stands above `public-api-surface`
  and `deprecated-apis`, which share its `appliesTo` phrases and answer the
  member question.

## Decided

- A second hint rather than a widened one. `D-KNW-030` makes a hint one
  question, and `public-api-surface` is titled and matched for a signature; the
  two now name each other, so a session that arrives at either half is told the
  other exists.
- The domains are `php`, `typoscript` and `fluid`, which is what the question is
  asked from. The first decides the category, and the hint stands under PHP
  beside the one it splits from.
- The four-type definition in `## Changelog Files` is corrected rather than
  extended. It dropped `affect` from its source, which is the word the whole
  reading turns on, and it gains one bullet naming the hint instead of restating
  it.
- The `breaking` intent's first checklist step names the second test and routes
  to the hint. It settled the question on whether anything outside the core
  calls the member, which is the wrong test for a change that calls nothing.
- The new hint carries the exemption and `public-api-surface` keeps its flat
  rule. A maintained line takes a breaking entry "only in rare exemptions",
  which is `Howto.rst`'s own wording, and the flat sentence beside it is about a
  signature change, where no exemption is on record at all.
- No count of the sweep reaches the corpus. A hint may state no version, no
  changelog file and no count taken from a checkout — `HintsTest` holds it — and
  the evidence belongs in a decision, where it carries its date.

## Assumed

- That the `.x` exemption stays rare. It was last taken in `8.7.x`, and a
  maintained line that takes one again makes the hint's "rare exemption" the
  right shape but its emphasis wrong.
- That a reviewer arrives with the vocabulary of the effect — markup, rendered,
  default TypoScript. The hint is reached by its own words rather than by a
  path, because a rendering change has no directory of its own.
- That the correction to `## Changelog Files` is where a rule lookup for this
  lands. Its document is declared for writing a commit message, and the
  classification question is asked from a review.

## Wrong if

- A core change to rendered output is filed as `Important` on `main`, outside
  the additive case the hint names. The branch would then not be what decides
  it, and the boundary would run somewhere this reading did not look.
- A maintained line carries a Breaking entry for a rendering change again. The
  exemption would be current rather than historical, and the hint's last two
  statements would be the wrong way round.
- A session reaches the hint and still answers the classification from memory.
  The lever is then the review checklist joining **Behaviour** to the changelog
  surface, which `D-KNW-072` named and this entry did not take.
- The same question is reported again phrased about TCA, YAML or a database
  column. Those are the same class and the hint names them in one clause, so a
  report would say the clause is not enough and each owes its own sentence.

## Since then

The fourth **Wrong if** happened and took the first **Assumed** with it: a
session reached both poles, judged the obligation itself and concluded no entry
was owed. The hint answers that case — its second statement names a removed
configuration option — so what failed is the assumption that a reviewer arrives
with the vocabulary of the effect. This one arrived with the vocabulary of the
obligation, and its case was configuration rather than markup.

 The casual-bugfix bullet refused the demand on "removes nothing public", which
 a fix that stops reading a configured option passes while owing an entry; it
 names what such a fix has to change nothing of now, with the hint one line
 above it. The section's lead is untouched deliberately: two sessions were saved
 by the flat exemption against one misled by it. The section is capped, and a
 first draft carrying both tests in full came back truncated — one delivery
 failure traded for another.
