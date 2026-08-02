---
id: D-ANS-010
date: 2026-08-02
status: open
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
  there**", and the entry for `typo3_documentation_lookup` named a source
  — "Needing the official API, reference or tutorial documentation" — which a
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
  [judging.md](../../documentation/feedback/judging.md) puts that on the
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

- A session follows the new routing entry, calls
  `typo3_documentation_lookup` at the target version, and still has to read the
  installed core by hand. Then the manual is not the answer for this shape and
  the feedback's own proposal — a capability that resolves behaviour rather than
  change — is what was missing after all.
- The skill half lands and a later conformance review reports the same "I had to
  read installed vendor core" ending. Then the order was not what kept the tool
  from firing.
- A feedback disputes the changelog's silence the other way — an entry that
  exists and was not reached — which would make this a matching problem rather
  than a routing one.

## Covered by

- `ScopeTest::everyToolNamedInTheScopeExists`
- `SkillTest::theChangelogsSilenceIsNotAnAnswerAboutWhatStillWorks`

## Since then

The skill half landed on 2026-08-02, as a sentence on the sweep rather than as a
step of its own. Two things settled that. The sweep can state its query set
before a file is opened because step 2 derives it, and a version-behaviour
question has none until the reading raises the pattern — so a sixth step would
be a call every task pays for with nothing to put in it. And the failure was not
that the question went unasked but that an empty changelog was read as its
answer, which is a statement about what step 5 is worth and belongs on step 5.
`skills/base.md` now carries it, which reaches every published skill through the
copy the installer writes; `typo3-extension-conformance` had the narrower
condition of the two — the manual "where an official API or configuration detail
decides the finding" — and now names the question shape as well.

The `Assumed` above got its second reading in the same run. Re-run from this
checkout on 2026-08-02, `typo3_documentation_lookup` at `targetVersion: "14"`
with `backend layout` still returns the two answering pages first and second,
against 14.3. The query for the second instance, the `ext_localconf.php`
content-rendering registration, returns TypoScript rendering objects and PSR-14
events and nothing that answers it — so the assumption holds for one shape and
not the other, and the wording says a miss in the manual is a finding rather
than a licence to reconstruct the contract from the installed core.
