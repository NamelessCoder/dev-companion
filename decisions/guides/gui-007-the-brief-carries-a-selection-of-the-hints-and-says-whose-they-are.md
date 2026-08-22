---
id: D-GUI-007
title: The brief carries a selection of the hints and says whose they are
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::theHintsABriefCarriesNameTheLookupTheyCameFrom
---

# D-GUI-007 — The brief carries a selection of the hints and says whose they are

**`typo3_task_guide` states that its hints are `typo3_hint_lookup`'s and that it
carries the strongest few of them, and the separate call to that lookup stays in
the review skill.**

A report following the review checklist cited two of the brief's hints as
`typo3_hint_lookup` without that tool having been called. Two corrections were
open, and which one is right is a fact about the two payloads rather than a
judgement.

## Evidence

- Measured on the call the fifth recorded `REVIEW-03` run actually made, against
  the server at `99785b8`. `typo3_task_guide` came back with
  `fluid-viewhelpers`, `system-extension-boundaries`, `core-tests` and
  `fal-basics`. `typo3_hint_lookup`, given the same five paths, the same task
  text and the same `targetVersion`, came back with those four in the same
  order, then `fal-reading` and `fal-processing` at its default limit, and
  `dependency-injection` at `limit=10`.
- Each of the four is the same record on both sides — same statements, same
  ranges, same scope markers. So the guide quotes rather than summarises, and
  the run's rules were correctly attributed even though the tool it named had
  not answered.
- The brief takes four per group of paths where the lookup's default is six and
  its ceiling ten. The lookup also answers a hint by `id`, lists the ids that
  exist when nothing matched, and says when a category was withheld because the
  task names the frontend. A brief has none of the three: `id` is not among its
  arguments, its miss branch is one sentence, and the withheld categories its
  own matcher computes are never rendered or returned.

## Decided

- The brief says whose the hints are where it prints them, and the `hints` field
  of the payload says the same. The correction sits on the answer that carries
  the copy, so it costs the caller nothing and reaches every client of the tool
  rather than the readers of one skill.
- The count is a named constant, `TaskGuide::HINTS_PER_GROUP`, and the sentence
  is rendered from it. The number a caller reads and the number the slice is
  taken at cannot then disagree.
- Rejected: taking the separate `typo3_hint_lookup` call out of
  `typo3-core-patch-review`, which `D-SKL-009` left open as the other candidate.
  Its premise was that the brief's copy makes the call redundant, and the
  measurement above refutes it — three of the seven hints for that patch's own
  paths are reachable only from the lookup, and one of them is
  `dependency-injection` on a patch that injects a new service.

## Assumed

- That a citation is what the run got wrong, rather than the call it did not
  make. On this task the four hints the brief carried were the ones the findings
  rested on, so the missing three cost that report nothing; what it cost was a
  reader following the citation.
- That naming the source beside the copy is read as attribution rather than as
  permission. The sentence says both halves for that reason, and the second half
  is the one under load.

## Wrong if

- A run cites the hints correctly and stops calling `typo3_hint_lookup` on a
  patch whose subsystem the four carried hints do not cover. The sentence would
  then be read as a substitute for the call, and what is left is to say in the
  skill what the brief cannot say — that four is not a subsystem sweep.
- `HINTS_PER_GROUP` is ever raised to the lookup's own default. The second half
  of the sentence stops being true where the brief carries everything the lookup
  would.

## Confirmed on 2026-08-03

The call this was decided from was made again, against the server on this
branch. The sentence is on the answer and rendered from `HINTS_PER_GROUP`, and
the brief still carries `fluid-viewhelpers`, `system-extension-boundaries`,
`core-tests` and `fal-basics`. `typo3_hint_lookup`, given the same five paths,
the same task text and `targetVersion` `15.0`, answers those four and then
`fal-reading` and `fal-processing` at its default, and `dependency-injection` at
`limit=10`. That is what was measured at `99785b8`, unchanged.

What the reading adds is the rejected alternative priced by the session that
proposed it. `feedback/2026-08-03-144410` is the same run's own debrief, written
at 14:44 and fifteen minutes before this sentence landed in `e306b02`. It argues
that the brief's four hints make the separate call a re-fetch, and then names
what skipping it cost: no dependency-injection hint, and
`#[Autowire(lazy: true)]` established instead by grepping three call sites out
of the checkout. The hint it wanted is the seventh the lookup holds for its own
paths.

Neither **Wrong if** has been reached. No run has been recorded since the
sentence landed, so nothing yet says whether it is read as attribution or as
permission, and `HINTS_PER_GROUP` is still four.

What the same feedback asks for beyond this entry is the subjects rather than
the count: the brief says it carries four and never says which three it left, so
the gap a caller would follow the pointer for is invisible until the call is
made. That is `R-GUI-012`, and it does not disturb what is decided here.
