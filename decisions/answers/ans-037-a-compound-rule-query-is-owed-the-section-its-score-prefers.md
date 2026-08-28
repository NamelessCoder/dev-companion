---
id: D-ANS-037
title: 'A compound rule query is owed the section its score prefers'
date: 2026-08-03
status: open
coveredBy:
  - KnowledgeTest::aMissInsideTheCoreNamesTheWords
  - KnowledgeTest::aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt
  - KnowledgeTest::aQueryThatNamesItsDocumentReachesTheSectionThatAnswersIt
  - KnowledgeTest::aSubsetIsNamedInTheWordsTheQueryWasWrittenIn
  - KnowledgeTest::anUnrelatedQueryAnswersWithNothing
  - KnowledgeTest::everyDocumentIsReachedByItsOwnTitle
  - KnowledgeTest::whatAMissOffersToAskAgainWithReturnsSections
---

# D-ANS-037 — A compound rule query is owed the section its score prefers

**A rule query longer than its topic is owed the section its score prefers, and
a miss that names the words rather than the core boundary.**

Coverage decides both halves of `typo3_rule_lookup` today. It ranks and it
gates, so two words naming the document drop the section that answers below the
floor; and where nothing survives it, the answer reaches its no-match path only
when the hints missed too.

`feedback/2026-08-01-115115` reports three answers that worked and closes with
the half nothing else in its session states: the compound queries failed and the
single-term ones worked. Read against the corpus that is the boundary
[`D-FBK-018`](../feedback/fbk-018-a-strength-is-evidence-about-a-boundary-not-about-a-decision.md)
says a strength carries — a topic is asked for in the words of a topic, and a
task sentence is not one.

## Evidence

- The strength reproduces whole, re-run on 2026-08-03 through
  `bin/typo3-dev-companion` from `/home/benji/projects/typo3-cms`, the directory
  it was written in. `typo3_project_describe` answers
  `core-checkout, TYPO3 15.0.0-dev, PHP ^8.5 declared and 8.5 in DDEV`, with
  `Extensions: none beyond TYPO3's own.` and
  `Sites: none configured below config/sites/.` — the report's four claims, in
  its own words. `typo3_rule_lookup "breaking change"` returns
  `## Breaking Changes` and `## Changelog Files` at 100% of the query terms, and
  the first of them states the `[!!!]` marker, the changelog RST and the
  extension scanner matcher. `typo3_commit_message_guide` on the patch's own
  subject answers
  `WARNING: The summary line is 68 characters long. Below 52 characters is preferred.`
- The compound query this session actually recorded is answered. It is in the
  sibling `feedback/2026-08-01-115109`, now archived, and
  [`D-ANS-029`](ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md)
  measured it missing on 2026-08-02. Re-run today,
  `removing public method extension scanner matcher breaking changelog` returns
  `## Breaking Changes`, because
  [`D-ANS-035`](ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md)
  put the matcher sentence in it.
- The shape the report names is still there, and it is not a miss.
  `commit message summary line length` returns `## One-Time Setup` and
  `## Release Branches and Backports` of the Gerrit workflow, both at coverage
  0.525 and score 38. `## Summary Line` — score 124, and the section carrying
  the 52-character rule the same session was flagged on — sits at coverage 0.429
  and is dropped by `Documents::MIN_COVERAGE`. `summary line length` returns it
  first at 0.659, and `summary line` returns it first too.
- Which two words cost it is exact. The weights are `commit` 0.92, `messag`
  1.61, `summar` 1.83, `line` 1.27, `length` 1.61. `## Summary Line` carries
  `summar` and `line`; the Gerrit sections carry `commit`, `messag` and `line`.
  So the section that is *about* the query is beaten by two that merely say its
  subject's name, and the score that knows the difference never gets a vote:
  `Documents::search()` sorts on coverage first and gates on coverage alone.
- Naming the document is pure cost. `Documents::searchable()` hands the matcher
  a section's heading and body, so the document title is in no searched field —
  `## Summary Line` does not contain the words *commit* or *message*, which
  stand in the title and the preamble of `typo3-commit-messages.md`. This is
  [`D-ANS-021`](ans-021-the-manual-lookup-says-why-a-short-query-ranks-better.md)'s
  finding on a second corpus, and worse in one way: there the subject term was
  merely cheap, here it is absent from the field being searched.
- Where a compound query does miss, the caller is told the wrong reason.
  `RuleLookup::answer()` reaches `noMatch()` only where the prose, the hints and
  the withheld documents are all empty; where hints matched, an empty prose
  result prints `No section that holds outside the core matched "<query>"`
  whatever the scope. The session's own task sentence, *review of core patch
  replacing GD error thumbnails with SVG placeholder*, gets that line from
  `/home/benji/projects/typo3-cms` with `scope: core` and
  `withheldDocuments: []` — a boundary that did not apply and withheld nothing.
- A judging run has already been misled by it. `D-ANS-029` quoted that sentence
  back on 2026-08-02 as what the tool answered a core-checkout query, and read
  it as an answer rather than as a claim about scope.
- The miss also drops the orientation the other path gives. `noMatch()` lists
  every document with its topics, which is what `R-ANS-006` holds this tool to
  and what a caller can ask for outright; the hint-matched miss lists neither
  those nor a sub-query that would have hit, which is what `D-ANS-016` built for
  the changelog. No test in `tests/` reaches either miss path of this tool.
- The curated callers are unaffected. `TaskIntents` puts short `rulesQuery`
  strings through the same search — the `breaking` intent's is
  `breaking change changelog`, which returns four sections today — so what moves
  here is the caller who writes their own sentence.

## Decided

- **Trimmed.** The three answers the report asks be kept reproduce in its own
  words, and keeping something is not work; the feedback is trimmed to its last
  sentence and stays open behind the two cards below.
- **The miss sentence is step 4, wording.** The tool was called correctly and
  answered honestly; what it delivered was a reason that is false inside the
  core and orientation the sibling path already has. Queued rather than closed
  on the spot, because it is `src/`.
- **The coverage floor is a gap in the gate rather than in the wording.** The
  scoring preferred the right section by more than three to one and a share
  computed over the query's own length overruled it, which is
  [`D-ANS-025`](ans-025-a-query-a-hint-carries-whole-is-not-diluted-out-of-it.md)'s
  mechanism from the other end: that entry read dilution by the length of the
  *text* and left the prose corpus alone, and this is dilution by the length of
  the *query*.
- **What replaces the floor is not named here.** Yielding the gate to the score,
  ranking the largest covering subset the way
  [`D-ANS-016`](ans-016-a-miss-names-the-query-that-would-have-hit.md) does, and
  weighting the document title into the searched fields are three different
  answers with three different costs over the whole corpus, and the run that
  judged this read one tool. The card carries the measurement.
- Recorded against the answer rather than against the search. `D-ANS-003` keeps
  retrieval lexical and nothing here asks it not to be.

## Assumed

- That a caller writes `commit message summary line length` or something like
  it. The feedback reports the shape without recording a query, so the phrasing
  is this run's reconstruction from what the session was doing — reviewing a
  commit message it had just been told was 68 characters long.
- That the two Gerrit sections are not the better answer. They carry three of
  five query words and nothing about how long a summary line may be, which is
  read off the sections rather than measured against a caller.
- That no other prose query is silently in this state. One was found by asking
  for a subject the corpus states in a section named after it; nothing swept the
  208 curated patterns or the scenario prompts against their own documents.

## Wrong if

- The floor is changed and a query that used to reach nothing starts returning
  the nearest unrelated section. That is what `MIN_COVERAGE` was put there to
  stop, and `Documents`'s own docblock is the promise it would break.
- The miss sentence is corrected and a later feedback still reports a compound
  query as having failed. Then what the caller could not see was the ranking
  rather than the reason, and the second card is the whole of the answer.
- A sweep finds `## Summary Line` is the only section this reaches, because the
  prose corpus is small and its headings already carry their documents'
  subjects. Then this is one section that needed a word, not a property of the
  gate.
- The score turns out to be the wrong tie-break, because a heading weighs four
  and a long section can out-score a short one that answers. `## Summary Line`
  won on a heading match here, which is the easy case.

## Since then

The third answer was built: the title joins the searched fields at a weight of
two, and the coverage floor is untouched, so what the first **Wrong if** is
about never moved. The three were measured over 490 queries.

Yielding the gate to the score returns the nearest unrelated section to 87
queries that reached nothing, almost every scenario prompt among them. Admitting
the largest covering subset returns the same 87, because a relative floor admits
its own best by construction — a question about sonnets included. The title in
the searched fields moves one first hit of 424 patterns, none of the prompts and
none of the headings, and takes nothing away from a query that reached
something.

## Since then

The miss half was built on 2026-08-03: the boundary is read as a reason only
where it withheld something, so an empty result with nothing withheld is the
miss answer whatever the hints did. Re-run, the query this entry was written on
answers the miss, then the per-word counts naming the subset that narrows best,
then the hints and the topic list — and both offered subsets return sections
when they are asked. The other path is unchanged and reachable.
