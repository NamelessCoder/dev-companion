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

`feedback/2026-08-24-100635` is the fourth **Wrong if** happening, and it took
the first **Assumed** with it. A session reviewing and then reworking Gerrit
95375 — the "Create new form" wizard's Blank mode stops reading
`formManager.selectablePrototypesConfiguration.*.newFormTemplates` — reached
`## Changelog Files` and `## Documentation`, judged the obligation itself, and
concluded that no entry was owed and that `Releases: main, 14.3` stood. Its own
account of the reading is that the two poles it was given left its case between
them.

The hint answers that case. Its second statement names a removed configuration
option, and the sweep behind it read `Breaking-106596`, which removes the legacy
EXT:form frontend templates and the YAML option that selected them. So the
assumption that a reviewer arrives with the vocabulary of the effect is what
failed: this one arrived with the vocabulary of the obligation, and its case was
configuration rather than markup.

Re-run on 2026-08-24, which is the judging evidence rather than a second reading
of TYPO3.
`typo3_rule_lookup(query="changelog entry review readiness", targetVersion="15")`
returns what the feedback quotes, word for word.
`bin/cli hints:probe "does this bugfix owe a changelog entry"` reached
`documentation-changelog` alone — the skeleton, not the test — and
`"integrator loses a configuration option changelog"` reached nothing. The
feedback reports the hint id arriving in `alsoInHints`; it does not, and that
field carries `documentation-changelog` alone. The hint's only delivery into the
question was one in-body sentence about `Breaking`, which is the word the reader
had already ruled out.

Judged step 4 on the ladder and closed on the spot, since nothing about TYPO3
had to be looked up. The casual-bugfix bullet refused the demand on the
condition "removes nothing public", which is what a fix that stops reading a
configured option passes while owing an entry all the same. It now names what
such a fix has to change nothing of — what an installation renders, is
configured by, or has documented — and the bullet directly above it is the one
carrying the hint, so a reader who fails a test has the route one line up.
`appliesTo` gained the obligation question and the configuration case, so
`bin/cli hints:probe "does this bugfix owe a changelog entry"` reaches the hint
first and `documentation-changelog` beside it.

The section's lead is untouched, and that is a decision rather than an omission.
`feedback/2026-08-08-224455` and `-224426` both name "A `BUGFIX` owes none" as
what stopped an `Important-*.rst` being written to be safe, and `KnowledgeTest`
has held the sentence since. Two sessions saved by the flat exemption against
one misled by it is not a case for removing it, so what changed is the condition
on the refusal below it, which was wrong rather than merely flat.

The section is capped at 2400 bytes by `Documents::MAX_SECTION_LENGTH` and now
weighs 2360. A first draft carrying both tests in full ran to 2731 and was
returned truncated, losing the `documentation-changelog` pointer off the end —
one delivery failure traded for another. What fits is the shorter clause, which
is also the one that does not repeat the bullet above it.

What this does not settle is the third pole the session named — a manual that
gains a paragraph it never had. On the reading above it is subsumed, because
text describing changed behaviour means the second test already fired, and text
filling a gap means nothing changed. Nothing here verified that against the
core, so it is written nowhere and stays what a later reading owes.
