---
id: D-ANS-081
date: 2026-08-18
status: open
---

# D-ANS-081 — A symptom is answered across the domain it was observed in

**A symptom names the layer a failure showed in, and the hint that explains it
is gated out of the answer by the domain it lives in.**

The second index the feedback asks for is already there: a hint's own statements
are searched beside its curated vocabulary, and the symptom is what those
statements are written in. What withholds the answer is the domain gate, which
is asked the same question the caller was: where does this belong. While
planning the two agree, because the words of a task name the layer the work is
in. Debugging is where they part.

## Evidence

- `feedback/2026-08-17-212010`, re-run on 2026-08-18 with `bin/cli hints:probe`.
  Its first query, "inline children are created but uid_foreign stays 0",
  returns `datahandler-relations` first on `appliesTo(11) + text(613)` — the id
  the feedback names, reached largely through the hint's own prose. Its second,
  "f:asset.css does not appear in the rendered page", returns
  `css-source-build-boundaries` and `public-assets` and not
  `fluid-layouts-sections`.
- The axis exists.
  [`R-KNW-021`](../../requirements/knowledge/knw-021-a-hint-is-reachable-by-what-it-says.md)
  scores a hint's statements, and `Hints::FIELD_WEIGHTS` weighs `title` and
  `appliesTo` at 4 against `text` at 1. Two of the hints the feedback names
  carry the symptom as curated vocabulary as well: `datahandler-placement` lists
  "reverse order", "reversed order" and "wrong order", `datahandler-relations`
  lists "relation not saved" and "children not linked".
- A third symptom from the same session shows what withholds them. "the content
  elements render in reverse order" returns `content-elements` and
  `content-element-shape`; `datahandler-placement`, which carries the query's
  own words, is neither returned nor listed in the index beside it. "content
  element" is a Fluid and TypoScript keyword in `Domains::KEYWORDS`, nothing in
  the sentence is a PHP one, and `Hints::find()` builds its candidates from the
  selected domains alone. The same sentence with "in the backend" appended
  selects PHP and returns `datahandler-placement` second.
- `availableHints` does not repair it. The refused set it is built from is the
  same domain-gated candidate list, so a hint the gate dropped is not offered as
  an id either.
- The miss on `fluid-layouts-sections` is the other cause and is lexical rather
  than gated: Fluid was selected and the query carries none of the hint's words.
  "a viewhelper call outside a section is never executed" reaches it on its
  prose alone, `text only(339)`.
- Nothing tells a caller that a symptom is a query `task` takes. The parameter
  reads "Short task description or topic", the `routing` block names this tool
  for "Working in a concrete file and unsure about the subsystem's conventions",
  and the session says it never tried the call it is describing.
- `feedback/2026-08-17-205945` is the same session on the same moment from the
  other side — which lookups it made after the first exception. Its card is in
  hand on another branch and reads the moment as routing; this entry reads what
  the matcher does with the query when it is made.

## Decided

- Not a second index. The corpus is not re-indexed under a "what it looks like
  when this goes wrong" field, because the field it would duplicate is already
  searched and answers where the symptom carries the mechanism's words.
- The gate is the gap, and it is queued as a measurement before a change. An
  exact `appliesTo` phrase is what a curator wrote for the query that should
  reach the hint, so letting one past the gate is the smallest candidate — and
  the sweep `R-KNW-021` is held by is what says whether it widens into answering
  everything.
- The `task` parameter says a symptom is a query it takes, in the words the
  measurement supports, and lands with it rather than before it. Promising a
  caller more than the matcher keeps costs the trust that made the call.
- Rejected: raising the `text` weight. That is the knob `UNDILUTED_WORDS` and
  `MAX_MEAN_BODY_WORDS` exist to keep still, and it would answer a symptom out
  of whichever hint is longest.

## Assumed

- That the gate is right everywhere else. It was measured on task descriptions,
  where the words of the query and the layer of the work are the same thing, and
  this entry claims only that debugging separates them.
- That the four cases the session reports are one shape. Three were checked here
  and two of them were gated; the fourth is a subject the corpus does not carry.

## Wrong if

- The measurement shows the gate paying for itself on symptoms too: the sweep
  loses recall, or hints from layers the query never meant come back once a
  curated phrase is let past.
- A session reports the opposite failure — a symptom answered from a layer it
  did not ask about — after the gate is widened.
- The next symptom reported as a miss turns out lexical like
  `fluid-layouts-sections` rather than gated, which would make this a curation
  task and not a matcher one.

## Covered by

- `HintsTest::theSweepTheMatcherWasMeasuredOnStillAnswersTheSameWay`
- `HintsTest::theCuratedVocabularyStillDecidesWhereItWasWritten`
