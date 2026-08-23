---
id: D-ANS-051
title: 'A manual result carries how much of the question it covers'
date: 2026-08-04
status: open
coveredBy:
  - DocumentationTest::aPageReadBackCoversNoQuery
  - DocumentationTest::aResultCoveringLessThanHalfTheQueryIsStillReturned
  - DocumentationTest::aResultCoversTheQueryItIsKeptFor
  - DocumentationTest::anAnswerThatCoversTheQuestionCarriesNoSuchSentence
  - DocumentationTest::everySearchResultSaysHowMuchOfTheQueryItCovers
  - DocumentationTest::theAnswerSaysWhereNothingCoversHalfTheQuery
---

# D-ANS-051 — A manual result carries how much of the question it covers

**Every live-manual result carries the share of the query it covers, and the
answer says above the results where nothing covers half. The floor is not
shipped: no value of it empties the reported queries and keeps a page that
answers today.**

`D-ANS-046` decided the floor and the miss as one change. Its own **Since then**
then measured that the floor has no value: at 0.5 the three queries of
`feedback/2026-08-03-164734` stop returning six collisions, and
`login screen layout` and `login form template` are emptied with them — the two
queries that entry offers as proof that *LoginProvider* is reachable at all.

## Evidence

- Every number below was measured on 2026-08-04 against the live 14.3 index this
  lookup builds — 1419 pages from the four manuals — with the coverage
  `TermSearch::score()` returns divided by `array_sum($weights)`, which is the
  share `Documents::search()` compares. `D-ANS-046`'s figures reproduce to the
  second decimal, a day later.
- The three reported queries: `fluid.html file extension templates` clears 0.5
  once, at 0.598 on *Multi-language Fluid templates*;
  `Fluid template file naming convention v14` clears nowhere at a best of 0.399
  on *Naming*; `layout root paths login screen override` clears nowhere at a
  best of 0.217 on *be.pagePath*. All six collisions the feedback reported come
  back, in the order it reported them.
- The three that price the floor: `login screen layout` returns *LoginProvider*
  fifth at 0.344 and tops out at 0.386; `login form template` returns it seventh
  at 0.37 and tops out at 0.422; `login provider` returns it first at 1.00. So a
  floor returning the first two is at 0.34 or under, and one emptying
  `Fluid template file naming convention v14` is above 0.40.
- Two queries `DocumentationTest` already holds go the same way:
  `TCA inline foreign_field foreign_sortby localization children` returns *IRRE
  / inline* first at 0.43, and
  `FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14` returns
  *Functional tests* first at 0.19. Both would be emptied at 0.5.
- Coverage is not aboutness on this corpus, and one of these six shows it. The
  page clearing 0.5 on the first query is *Multi-language Fluid templates*,
  which is the first collision the feedback reported: the query's weight sits in
  `fluid.`, `extens` and `templa`, and a page about localizing Fluid templates
  carries all three.
- The sentence fires on four of the six queries above. It is silent on
  `fluid.html file extension templates` and on `login provider`, which is the
  one query of the six whose first result answers it.
- `bin/cli feedback:list` on 2026-08-04: 3 open in two directories, each with a
  todo naming it. The 25 of `D-ANS-046` were worked off in between.

## Decided

- **The second of the three options the card priced, chosen by the maintainer on
  2026-08-04.** It is what the reported session could not see, it costs no query
  that answers today, and it leaves the floor available once there is a measure
  for it.
- **A schema addition, and additive.** `coverage` joins the properties of a
  result and its required list; every existing field keeps its name and its
  meaning, and the schema stays open, so a client validating against the old one
  is unaffected. Nothing is dropped, so the population of an answer is exactly
  what it was — the same six results, each now saying how much of the question
  it carries.
- **Null in page mode rather than zero.** Nothing was searched for there, and a
  zero would say the page answers nothing. It is the null beside the empty
  `matched`, which is the same statement about the same mode.
- **The warning is a sentence and the share is a number.** A number in a payload
  is not a warning: what the feedback reports as the expensive kind of wrong
  answer is six results in the shape a good answer has, and a client that reads
  only the text half is the one that gets there. So the text says it once above
  the results, and the data half carries no second flag for a client to
  interpret.
- **0.5 is `Documents::MIN_COVERAGE` and not a number of this tool's own.** The
  two corpora this server matches prose against say "half the query" with one
  value; there it drops a section and here it labels a page.
- **The corpus sentence naming `typo3_changelog_lookup` is not shipped.**
  `D-ANS-043`'s shape hangs on a miss, and this change produces no miss. It
  belongs to the floor whenever that ships.
- **`feedback/2026-08-03-164734` is not archived.** Its suggestion's "at
  minimum" half is built; its second sentence — return the fact rather than the
  best six collisions — is not, and that is the floor. The feedback is trimmed
  to it and the card keeps waiting on the measure.
- **`Documentation::useReader()`, so the text half is driven by a test.** The
  tool builds its own `Documentation`, so a fixture had nowhere to go and the
  sentence would have been held by docs.typo3.org answering during a test run.
  `R-COD-003` says a unit test stubs what is outside it, and this is the seam
  `Typo3Cli::useRunner()` already is for the console.

## Assumed

- That a caller reads the sentence and the share. Nothing here measures it.
  `D-ANS-021`'s **Since then** is the nearest evidence in the other direction: a
  session did read the match line this one sits beside, described the ranking
  from it correctly, and still went to its vendor tree.
- That labelling is worth more than emptying to a caller that has already paid
  for the call. The alternative was measured and not preferred; neither was
  tried on a session.
- That the share is the right measure to report even where it is not aboutness.
  The 0.598 above is a wrong page clearing the half, so a caller reading the
  number as "this page is about my question" is reading more into it than it
  says.
- That the numbers hold as the manuals grow. They are the 14.3 indexes on one
  day, and `D-ANS-040` recorded a query falling from 0.508 to 0.462 because four
  sections were added elsewhere in another corpus.

## Wrong if

- A session is handed a 19% answer, reads it as a full one, and reports the same
  ending — the question settled by reading installed source. That is the case
  this entry is most exposed to, because a number in a payload is not a warning
  and neither, perhaps, is a sentence above six confident results. Then what is
  left is the floor or the index, and the floor still needs its measure.
- The sentence fires on nearly every call. Four of the six queries measured here
  carry it; a corpus where most calls do is one where the sentence is wallpaper
  and the half is set against the wrong corpus.
- A feedback reports the opposite cost — the sentence above an answer whose
  first result is the right page. `login screen layout` is that case waiting to
  happen: *LoginProvider* is reachable there at 0.344 and the answer now says
  nothing covers half the question.
- Somebody sets the floor from this number without the measure the card asks
  for, and `login screen layout` stops returning *LoginProvider*. What would
  have to exist first is a share that survives a three-word question over a
  table of contents whose ordinary field is 2.66 words — measured against what a
  page could carry rather than against the whole query's weight — and an offer
  that carries what the floor drops, which `Search\Subsets::largestReaching()`
  does not today: it offers `layout login`, which returns three of the six
  collisions and not *LoginProvider*.

## Since then

The fourth **Wrong if** names what a floor would need before anybody sets one,
and both halves of it were measured on 2026-08-04 — the same day, over the same
live 14.3 index of 1419 pages, driving this lookup through a cached reader so
the numbers are the ones a caller gets. The maintainer asked for the rank gap
first and for the normalised share after it. Neither separates the six
collisions from the pages that answer a three-word question, and the two
failures have one cause.

### The rank gap, in three forms

Top-hit coverage against second-hit coverage over the eight queries above, as a
difference and as a ratio. As a difference the answers span 0.024 to 0.458 and
the collisions 0.020 to 0.294, so
`FunctionalTestCase executeFrontendSubRequest CSV fixture TYPO3 14` is an answer
lying below two of the three collisions. As a ratio the two bands sit on top of
each other: 0.542 to 0.871 against 0.508 to 0.908. Measured in the ranking score
instead of the coverage — which is what "one page well clear of the rest"
actually means, and the ranking is by score — it is the same picture, 0.129 to
0.457 against 0.093 to 0.327.

The gap does worse than fail to separate: it orders the two queries that price
the measure at the far collision end of both scales. `login screen layout` has
three candidates on one score at the top and `login form template` has five, so
both stand at a difference of 0.000 and a ratio of 1.000. Any threshold in
either form empties those two before it touches a collision, and takes
*LoginProvider* with them. A corpus of two- and three-word titles ties at the
top whenever the query is short, which is the shape the gap was supposed to
read.

### The share against what a page could carry

The measure this entry's fourth **Wrong if** asks for, made concrete as the
covered weight over the weight of the terms at least one page carries — asked
with `TermSearch::carries()` over the same searchable fields the score uses. A
term no page carries is weighed `log(N)/2` by `TermSearch::weights()` and can
never be covered by anybody, so today it lowers every page alike; here it leaves
the denominator instead.

It moves the numbers and it moves them the right way. `login screen layout`
reaches 73% of its own weight because no page carries "screen",
`TCA inline foreign_field foreign_sortby localization children` 62% because none
carries "foreig" or "childr", and the `FunctionalTestCase` query 75% because
none carries "sub", "csv" or "fixtur". So *Functional tests* goes from 0.186 to
0.247, *IRRE / inline* from 0.430 to 0.690, and *LoginProvider* in
`login screen layout` from 0.344 to 0.471.

And it reorders exactly one pair. The best candidate of
`layout root paths login screen override` moves only from 0.217 to 0.244, so the
answer that stood below that collision now stands above it: a floor in the
interval 0.244 to 0.247 empties that one query and keeps all five that must
survive. Three thousandths wide, and it leaves the other two collisions where
they were.

Those two are what closes the family. A floor would have to sit above 0.598 and
at or below 0.247, because *Multi-language Fluid templates* carries 0.598 of
`fluid.html file extension templates` and *Naming* carries 0.458 of
`Fluid template file naming convention v14`, while *Functional tests* carries
0.247 of the query it answers first. Neither of those two is a measurement
artefact that a better denominator repairs — both pages genuinely carry the
words they were found by, and both answer a different question than the one that
was asked.

That is one finding across all three measures rather than three separate
failures. What is indexed is a table of contents: what a page is called, where
it sits, and which book it is in. Carrying a query's words and answering its
question are not distinguishable in that, so no arithmetic over the coverage
separates them, and the remaining lever is the index rather than the threshold.
`D-ANS-046` is what stands in the way there for two of these three questions:
TYPO3 Explained 14.3 writes `.fluid.html` in 49 code-example captions and states
the convention in no sentence, so a corpus of page bodies does not carry the
answer either.

### The drop is closed out

Put to the maintainer on 2026-08-04 with the two options priced — close it out,
or keep the card open against a change of index that nothing has costed — and
the answer was to close it out. So the floor is not deferred any more: nothing
is left waiting on a measure, and building one would start from the reading
above rather than from this entry's fourth **Wrong if**.

`feedback/2026-08-03-164734` is archived with that, which supersedes the
**Decided** bullet saying it is not. Its "at minimum" half shipped in this
entry's change; its second sentence — return the fact that nothing clears a
threshold, rather than the best six collisions — is what is now declined, on the
grounds that no threshold over this index can tell the two cases apart. The card
that carried it is deleted.

What holds from here is what the change already ships: every result carries the
share of the query it covers, and the answer says above the results where
nothing covers half. The fourth **Wrong if** stays as it stands, because it is
still what somebody setting a floor would have to satisfy — the difference is
that it is now a bar rather than an errand.
