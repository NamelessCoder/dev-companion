---
id: D-DOC-003
title: A decision says what came back, and a requirement says what it rests on
date: 2026-08-02
status: confirmed
coveredBy:
  - DecisionsTest::aStatusNamesTheLastDatedLineInTheFile
  - DecisionsTest::everyTestADecisionNamesExists
  - UnresolvedTest::theOpenDecisionsAreReadOldestFirst
---

# D-DOC-003 — A decision says what came back, and a requirement says what it rests on

**The decision states are `open`, `confirmed` and `revoked`, an entry is written
in sections rather than labelled bullets, and a requirement names the decisions
it rests on.**

`corrected` was one word for four outcomes, and the status is the only thing a
reader has before opening a file that may be months old. Naming what a
requirement stands on is what makes a revoked decision under it audible rather
than silent.

## Evidence

- The twelve entries carrying `corrected`, read on 2026-08-02. On three it meant
  reversed and replaced — D-AUD-002, D-KNW-003, D-SCO-008. On three the **Wrong
  if** had fired with nothing replacing it — D-FBK-003, D-KNW-001, D-CAT-002. On
  three one named part was wrong and the rest held; D-FBK-005 says so outright:
  "the order this entry is mostly about … is untouched … what was wrong is the
  number beside it". On D-DIS-003 the **Wrong if** had explicitly *not* fired.
- A decision that was confirmed and later revoked could not be written down.
  D-KNW-003 was confirmed by a run on the morning of 2026-08-02 and revoked by
  the evidence that arrived the same day; the check rejected an entry carrying
  both lines, so the run had to be folded into the revocation prose and the
  two-stage history was lost.
- Twelve entries name a test in prose and nothing read it. One of the names in
  `D-KNW-003` was already stale — a `KnowledgeTest` method renamed that morning
  — and the entry still read as though something held it. The check written here
  caught it on its first run, and caught a second stale name in this entry's own
  first draft.
- `R-FBK-007` rests on `D-FBK-005`, which is revoked. The requirement is `held`,
  its test passes, and nothing anywhere says the reasoning under it was
  disproved.

## Decided

- Three states, not four. `amended` was weighed for the three partly-wrong
  entries and dropped: a reader who must not build on part of an entry is better
  served by `revoked` plus a dated line naming what fell than by a state that
  says "some of this is safe" without saying which.
- `open` rather than `standing`, and it is not a workflow step. An entry is
  written by the commit that implements it, so every open decision is already in
  the code; `open` says nobody has been back to the **Wrong if**. `confirmed`
  rather than `tested`, because several of these readings are readings rather
  than runs.
- The status names the last dated line. An entry may carry several, and what a
  reader relies on is the latest.
- `Covered by` is optional. Most entries here are about process and nothing runs
  over them, so requiring it would have produced a field saying "nothing" on two
  thirds of the directory.
- Every test named *anywhere* in an entry has to exist, not only the ones under
  `Covered by`. The prose makes the same claim and goes stale the same way.
- A requirement resting on a revoked decision is read out by
  `bin/cli unresolved:list` and fails nothing. Whether it still stands is a
  judgement, and AGENTS.md holds that no check may fail on a state that is
  legitimately unfinished. What does fail is `restsOn` naming an id no decision
  has.
- The generated listing is two lists — what still holds, then what was revoked —
  and a revoked entry shows its successor from `revokedBy`. One list of 55 rows
  made a revoked entry look exactly like something to build on.
- The body is `## Evidence`, `## Decided`, `## Assumed`, `## Wrong if`,
  `## Covered by` and a dated section per later reading, each holding bullets.
  It was a flat list of bullets carrying a bold label, and the label repeated:
  measured over the 56 entries, `Decided` carries more than one item on 25 of
  the 53 that have it and `Assumed` on 22 of 49, up to seven in one entry. A
  section says the word once. The dated sections carry prose instead of bullets,
  because each is an account of one reading rather than a list.
- `Covered by` is a list, one test per line, rather than a comma-separated
  sentence. It is read by a check, and a list is what it is.

## Assumed

- The eleven `restsOn` links, taken from what each requirement already named in
  prose, are the real dependencies. Nothing checked whether a requirement rests
  on a decision it never mentions, and 112 of the 123 name none at all.
- `revoked` is not so heavy that a session avoids using it and leaves an entry
  `open` instead. The word is stronger than `corrected` was, and that is the
  point, but it is also the failure mode.
- The 56 entries survived being rewritten by script — bullets to sections,
  re-wrapped to 79 columns and sentence-cased where the label had carried the
  capital. The re-wrap broke 30 code spans across lines before a second pass
  made a span unbreakable, which `D-DOC-001` is the entry against and which
  nothing would have failed on. `bin/cli decisions:check` holds the shape and
  `ProseTest` the sentences; neither reads for a sentence that lost its sense.

## Wrong if

- An entry appears whose outcome is none of the three — most likely one where
  the decision still holds but the world it was decided in is gone, which is
  `D-SCO-001`'s shape and was filed under `corrected` because there was nowhere
  else.
- `restsOn` stays at eleven entries. A crossing nobody maintains reports
  nothing, and a report that is always empty is read as "nothing is wrong"
  rather than "nothing was recorded".

## Confirmed on 2026-08-22

The second **Wrong if** is answered and the crossing is maintained. `restsOn`
stood on eleven entries when this was written and stands on 108 of the 222
requirements today, so the report is a reading of what is recorded rather than
one that is always empty — `R-AUD-002` was corrected on the strength of it the
same day this was read.

The three states carry the corpus: 339 open, 66 confirmed and 39 revoked over
444 entries, and 33 of the 39 name a successor in `revokedBy`. The sections held
too — `bin/cli decisions:check` fails on any other shape, and the dated sections
have since been given the same standing, which is what `D-DOC-038` and
`D-DOC-039` are.

The first **Wrong if** is not settled here. Whether an entry has appeared whose
outcome is none of the three is a judgement per entry rather than a sweep, and
what would show it is a session reaching for a fourth word — nobody has, and the
six revoked entries naming no successor are the nearest thing to the shape,
because a revocation with nowhere to send the reader is the one this format
cannot express.
