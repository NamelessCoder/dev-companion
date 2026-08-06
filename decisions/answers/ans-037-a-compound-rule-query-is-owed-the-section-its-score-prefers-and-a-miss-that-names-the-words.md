---
id: D-ANS-037
date: 2026-08-03
status: open
---

# D-ANS-037 — A compound rule query is owed the section its score prefers, and a miss that names the words

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
  it was written in. `typo3_project_scope` answers
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
  [`D-ANS-021`](ans-021-a-manual-query-is-told-what-short-buys.md)'s finding on
  a second corpus, and worse in one way: there the subject term was merely
  cheap, here it is absent from the field being searched.
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

## Covered by

- `KnowledgeTest::aQueryThatNamesItsDocumentReachesTheSectionThatAnswersIt`
- `KnowledgeTest::everyDocumentIsReachedByItsOwnTitle`
- `KnowledgeTest::anUnrelatedQueryAnswersWithNothingRatherThanTheNearestProse`
- `KnowledgeTest::aMissInsideTheCoreNamesTheWordsRatherThanTheBoundary`
- `KnowledgeTest::aMissThatWithheldADocumentSaysTheBoundaryEmptiedIt`
- `KnowledgeTest::whatAMissOffersToAskAgainWithReturnsSections`
- `KnowledgeTest::aSubsetIsNamedInTheWordsTheQueryWasWrittenIn`

## Since then

The third answer was built on 2026-08-03: `Documents::FIELD_WEIGHTS` gains
`title => 2` and the matcher is handed the document title. `MIN_COVERAGE` is
untouched, so the floor the first **Wrong if** is about never moved.

The three were measured over 490 queries — the 424 multi-word `appliesTo`
patterns that name no path, the 41 scenario prompts, the 20 section headings and
the 5 document titles.

- Yielding the gate to the score, admitting every section a query term reaches
  and ranking by score, returns the nearest unrelated section to 87 queries that
  reached nothing, 40 of the 41 prompts among them. It moves 28 first hits
  besides.
- Admitting the largest covering subset, with coverage measured against the best
  cover any section reaches, returns the same 87. A relative floor admits its
  own best by construction, so «how do I write a good sonnet» is answered too.
- The title in the searched fields moves one first hit of the 424 patterns —
  `commit the build`, from `## Changelog Files` to `## Breaking Changes` of the
  same document — none of the prompts, none of the headings, and takes nothing
  away from a query that reached something.

`commit message summary line length` now returns `## Summary Line` first at
score 175 and coverage 0.778, and the two Gerrit sections last at 0.525. The
weight is 2 because a title says what every section under it is about, which is
worth more than a passing mention in a body and less than the section's own
heading; 1, 2 and 3 are indistinguishable over the sweep, and 4 moves a second
first hit.

The title counts for the score and not for the term weighting, which is what
`Documents::distinguishing()` is. One title is repeated in every section of its
document, so weighing terms over it makes a word look common in proportion to
how many sections that document has — counted there,
`commit message sitepackage` lost the commit conventions it is answered with.

The third **Wrong if** half held. No caller query in the sweep was in
`## Summary Line`'s state, so nothing says the gate is crowded; the same gap
sits on the corpus's own front doors instead, where four of the five documents
were not reached by their own title and one reached nothing at all. All five
reach their own document first now.

What it costs is the order inside a document that a query names and no section
of it answers: every section covers the same terms at the same score, and the
ranking falls to the heading. `commit message sitepackage` outside the core is
six sections of the commit conventions with nothing withheld — the core-only
documents are outranked rather than dropped at the boundary, which is why
`ScopeTest::aRuleAnswerKeepsWhatTransfersAndWithholdsWhatDoesNot` no longer
asserts that something was withheld. The query in
`whatARuleAnswerWithheldIsNamedRatherThanMissing` reaches both halves and holds
it.

## Since then

The miss half was built on 2026-08-03. `RuleLookup::answer()` reads the boundary
as a reason only where it withheld something: an empty prose result with an
empty `withheldDocuments` is the miss answer whatever the hints did, and the
outside-core sentence stands where a document really was dropped for it. Re-run
from `/home/benji/projects/typo3-cms`, the query this entry was written on
answers *No knowledge section matched …*, then *No section carries more than 2
of the 8 words: "patch replacing" reaches 1 section, "review patch" reaches 7
sections — ask again with the one that narrows best*, then the two hints it
already carried and the topic list. Both offered subsets return sections when
they are asked. The other path is unchanged and reachable: *how do I push a
patch for review from my site package* withholds the Gerrit workflow and says
so.

The subsets are `D-ANS-016`'s computation on a second corpus, and what that took
was the matcher. `Search\Subsets` holds the one pass and is handed the corpus's
own `carries()`, because a subset offered on the labels' substring rule would
name sections a prose re-query does not return. They are counted over the
documents the call may answer from, so a subset offered outside the core is not
answered with the withholding notice, and what is named back is the caller's own
spelling rather than the stem it was reduced to — `TermSearch::words()` —
because both re-query to the same sections and only one of them reads as a typo.
The sentence around them is `Result\Miss`, which the changelog miss now prints
from as well, so the two cannot drift apart in wording.

What differs from the changelog is the count. `Documents::search()` keeps a
section covering half the query's weight, so *"patch replacing" reaches 1
section* returns two — a floor rather than the length of the answer, where the
changelog's number is exact because a hit there carries every word. It is
computed the same way for every subset, which is what the caller picks one by.
