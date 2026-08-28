---
id: D-ANS-010
title: '"Does it still work" is a question for the manual, not the changelog'
date: 2026-08-02
status: open
coveredBy:
  - ScopeTest::everyToolNamedInTheScopeExists
  - SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks
---

# D-ANS-010 — "Does it still work" is a question for the manual, not the changelog

**"Does this still work in version N" is routed to the manual for that version,
not to the changelog, which is silent on everything nothing changed.**

A changelog records change events. A pattern that has worked unaltered for ten
majors has no entry, so an empty result reads as "nothing found" where the
correct answer is "still supported".

## Evidence

- `feedback/2026-07-31-174524`, re-run on 2026-08-02 against
  `/home/benji/projects/bootstrap_package`. `typo3_changelog_lookup` with
  `query: "BackendLayout"` returns 11 entries now rather than the one the
  feedback reports — `D-ANS-006` landed since, and the query reaches "backend
  layout" however it is spelled. None of the 11 answers what the session asked.
- `typo3_documentation_lookup` with `targetVersion: "14"` and the query
  `backend layout` returns the two pages that do answer it, first and second.
  Reading the TSconfig one with `page` settles it in that call: `identifier` is
  documented as what the page content DataProcessor addresses a column by, "a
  more meaningful representation than just colPos", while `colPos` is what
  carries the content elements. The session read `GridColumn.php` in the
  installed core by hand and still recorded the finding as unverified.
- `bin/cli hints:probe "BackendLayout"` reaches nothing about backend layouts,
  and neither `knowledge/` nor `skills/` carries the words at all. That is not
  the gap. The manual owns this subject and answers it, so there is nothing to
  write here — only something to point at.
- The routing block pointed the other way. `typo3_changelog_lookup` was routed
  for "before asking what a version changed **or whether an API is still
  there**", and the entry for `typo3_documentation_lookup` named a source —
  "Needing the official API, reference or tutorial documentation" — which a
  session holding a behaviour question does not recognise as its own.
- The same session reported the same shape twice more.
  `feedback/2026-07-31-174526` ends "No lookup covers whether such a
  registration is still consumed in the active version: I had to read installed
  vendor core", and `feedback/2026-07-31-174529` names the pair "per-version
  behavior questions".

## Decided

- The judgement is **step 3 of the ladder**, routing, not the missing capability
  the feedback proposes. The tool that answers exists and answered in one call;
  nothing pointed the question shape at it.
- The routing half is **closed on the spot**. The false clause is out of the
  changelog entry and an entry for the shape now names
  `typo3_documentation_lookup` with `targetVersion`.
  [judging.md](../../documentation/records/judging.rst) puts that on the
  autonomous side: it touches no `src/`, no declared schema and no skill
  contract, and it writes no statement about TYPO3.
- The skill half is **queued**, because the routing block reaches a session only
  through `typo3_server_scope`. The order this session actually followed is
  `skills/base.md`, where the changelog sweep is a numbered step and
  `typo3_documentation_lookup` is a conditional bullet under it. That is a skill
  contract and is reviewed rather than improvised.
- Recorded here rather than against `typo3_changelog_lookup`, because the
  property belongs to every lookup over a record of events. What is asked of the
  changelog is what happened; whether something holds today is asked of the
  reference for the version.

## Assumed

- That the manual answers this class of question at the covered versions. One
  case is verified above and the second instance, the inert
  `contentRenderingTemplates` registration, is not — a magic key nothing
  consumes may well be documented nowhere.
- That a routing entry phrased as a question reaches a caller that an entry
  phrased as a source did not. Nothing measures which of the two a session
  matches itself against.

## Wrong if

- ~~A session follows the new routing entry, calls `typo3_documentation_lookup`
  at the target version, and still has to read the installed core by hand. Then
  the manual is not the answer for this shape and the feedback's own proposal —
  a capability that resolves behaviour rather than change — is what was missing
  after all.~~ Fired in half on 2026-08-03, and the subject narrowed instead:
  the manual answers a documented surface, and for a PHP identifier the routing
  terminates at the class. The capability stays unbuilt.
- ~~The skill half lands and a later conformance review reports the same "I had
  to read installed vendor core" ending. Then the order was not what kept the
  tool from firing.~~ Fired on 2026-08-03 from a core patch review, which quoted
  the sentence back. What it showed is where the sentence stands rather than
  what it says: the miss is where the caller is standing when the changelog
  comes back empty.
- ~~A feedback disputes the changelog's silence the other way — an entry that
  exists and was not reached — which would make this a matching problem rather
  than a routing one.~~ Fired on 2026-08-02 and again on 2026-08-03. The
  matching was sound both times and the query shape was not, which is
  `D-SKL-003`, and the miss is what tells a genuine silence from one asked for.
- A session has both — the statement in reach and the identifier named in the
  changelog — and still no way to tell whether the thing exists in the version
  it runs on. That is what would trigger the capability the first bullet
  reserves. Written on 2026-08-03, from the third reading below.

## Since then

The skill half landed as a sentence on the sweep rather than as a step of its
own, and two things settled that: the sweep can state its query set before a
file is opened while a version-behaviour question has none until the reading
raises it, so a sixth step would be a call every task pays for with nothing to
put in it — and the failure was not that the question went unasked but that an
empty changelog was read as its answer, which belongs on the step that produced
the silence. The base carries it, which reaches every published skill through
the copy the installer writes.

## Since then

The second and the third **Wrong if** fired together in one report, and neither
answer is the one they name: a core patch review ends the same way an audit did,
with an empty changelog and then grep over the installed core. The session
quotes the order back — it had read the routing and never called it — so the
order reached it and did not fire at the moment the silence arrived. That says
where the sentence stands rather than what it says.

The third is the sharper half: the silence was not genuine. The changelog
carries the entry that answers the load-bearing question of that review, and the
reported query returns it.

## Since then

The first **Wrong if** fired in half, and what decides the half is the corpus
rather than the routing: a session followed the routing and read the class by
hand anyway. Re-run, the two shapes come apart — a ViewHelper's name returns the
reference page carrying the answer whole, and three phrasings of a PHP
identifier return index and reference pages that name the method nowhere.

The tool's own header says why: it matches page titles and section paths, never
the text of a page, and a PHP identifier has no page to be titled after. So the
entry stands and its subject narrows to a documented surface; for an identifier
the routing terminates nowhere and the step after it is the class.

## Since then

Two readings built what the reading above queued and established nothing beyond
it, so each is a line here rather than a section of its own. Judged on
2026-08-22.

- Step 4 on 2026-08-03, in `skills/base.md` and not in the conformance skill:
  what the base now says at the point of the call is which corpus a question
  has. A documented surface goes to the manual, an identifier to the changelog
  under its own name and then to the class, and the miss-is-a-result sentence
  stands for the surface it was written about. The conformance skill defers to
  the base for why the changelog cannot answer, so a bound written there would
  leave the sentence it bounds unqualified in every published copy. It cost 79
  words, and `D-SKL-001` keeps that arithmetic.
- Step 1a on 2026-08-03: what makes a reading of `@deprecated` alone conclusive
  is the kind of member. A class constant and an enum case are read without
  anything in the declaring class running, and PHP's `#[\Deprecated]` attribute
  occurs nowhere in `typo3/sysext` on any covered line; for a method, a property
  or a class the same absence says nothing, and 58 of the 98 files carrying
  `@deprecated` in `.checkouts/14.3` hold no `trigger_error`. So the entry that
  announces a deprecation can be wrong about what it raises, which
  `Deprecation-107648` is. `deprecated-apis` states both halves and
  `HintsTest::whatADeprecationCarryingTheDocblockAloneRaisesIsStated` holds
  them; its opening sentence went with the change, because "this server does not
  know your branch" is true of the bundled corpus and not of the server.
