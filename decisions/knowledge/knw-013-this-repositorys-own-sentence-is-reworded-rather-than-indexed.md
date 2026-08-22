---
id: D-KNW-013
date: 2026-08-02
status: open
---

# D-KNW-013 — This repository's own sentence is reworded rather than indexed

**The conformance checklist's quality surface now names the test suite and the
supported TYPO3 versions it runs on, and `extension-repository-layout` carries
`supported TYPO3 version` and `cross-version`. No sixth domain keyword was
added.**

`D-KNW-009`'s first **Wrong if** asked what to do when a testing question still
misses the PHP domain through a phrasing nobody wrote down, and said the answer
would be another entry. The phrasing arrived from inside: the checklist wrote
its own audit surface as "Quality: tests, the check layer, documentation,
deprecations, and upgrade readiness", and the bare `tests` is the word that
entry deliberately rejected. An audit asking in the checklist's own wording
resolved to the `docs` domain alone, and the rule about the supported range — a
matrix that resolves per supported version, the lowest and the highest of each
supported major — was reached by neither hop.

What that cost is the feedback of 2026-07-31 19:36 UTC: a conformance audit
whose recommendation 19 was to "Consider typo3/cms-compatibility package for
cross-version testing", a package no covered line ships.

## Evidence

- The measurement `D-KNW-009` names, repeated over the 107 texts this repository
  now has to hand — 41 scenario prompts and 66 hint titles — plus the eleven
  testing queries `HintsTest` and that entry hold. All four candidates were run
  against the same corpus and diffed against the same baseline.
- **A phrasing in `Domains::KEYWORDS[PHP]`** cannot be written for that sentence
  except as the bare `test`, so that is what was measured. It moves three texts
  and buys nothing: the checklist query gains the PHP domain and still reaches
  neither testing hint, while "browser tests for the site package" loses
  `extension-repository-layout` from its answer and gains
  `sitepackage-initial-content`. The reweighing `D-KNW-009` rejected it for is
  smaller than it was — `test coverage` is in the vocabulary now, so `SKILL-01`
  no longer moves — and the keyword still pays for nothing.
- **A statement about the supported range in `project-extension-tests`** moves
  nothing at all: none of the 107 texts, and neither of the two queries. It is a
  second copy of a statement the corpus already holds, and reach is not what it
  changes.
- **Vocabulary on `extension-repository-layout`** decides the second hop.
  `supported TYPO3 version` and `cross-version` move two texts and only towards
  the rule: `EXT-05` — "wire them into our GitHub Actions so every pull request
  runs them against all supported TYPO3 versions" — leads with the matrix rule
  and still returns the harness hint, and "does the test suite cover every
  supported TYPO3 version" earns it at `appliesTo(23)` instead of reaching it
  text-only in second place. Nothing else in the corpus moves.
- Any pattern carrying a form of the word *test* costs more than it buys. Three
  of them — `cross-version testing`, `test matrix`, `CI matrix` — lift the
  hint's whole `appliesTo` field against every testing query, and "how do I test
  my extension" then leads with a layout hint on a score tie broken by title.
  The two patterns that name a version rather than a test do not.
- **The checklist's own sentence** decides the first hop, and nothing else can:
  with the vocabulary in place, the original wording still returns `docs` and no
  testing hint. Reworded to "the test suite and the supported TYPO3 versions it
  runs on", the same query resolves to `php, docs` and leads with
  `extension-repository-layout` at `appliesTo(23)`. Through `typo3_hint_lookup`
  with an extension's paths it returns that hint first, ahead of the
  `scope: project` layout hint it answered with before.

## Decided

- The sentence was reworded rather than indexed. A surface written as one bare
  noun is what made the query unanswerable, and widening the vocabulary to meet
  a word this repository chose is paying for its own phrasing twice: the keyword
  lists get longer and the sentence stays imprecise. The Quality surface is the
  suite *and* the versions it runs on, which is the half the feedback's audit
  never asked about.
- `D-KNW-009`'s **Decided** holds and is what the two halves are: the checklist
  sentence is the domain hop, the two patterns are the hint hop, and neither
  reaches the rule without the other.
- No sixth entry in `Domains::KEYWORDS[PHP]`, and the bare `test` stays out on
  the same measurement that kept it out before.
- No second statement of the range rule in `project-extension-tests`. Where a
  rule is written is a corpus question and this one is answered — the matrix
  belongs with the repository whose lock file it stands in for — and a copy that
  moves no query is only a second thing to keep true.

## Assumed

- An audit asks in the checklist's words. The skill tells it to write the
  surface list down from the checklist and then to query one surface at a time,
  so the sentence is the query, but nothing measures what a model actually sends
  and the reported audit is one session.
- `cross-version` is what a caller writes for the subject. It is the feedback's
  own word and nothing else in the 107 texts uses it, so it is a phrasing with
  one witness rather than a measured one.

## Wrong if

- A conformance audit reaches the matrix rule and still recommends a package for
  cross-version testing. The reach is then not the failure and the rule's own
  wording is, which is a question for the hint rather than for the vocabulary.
- The Quality surface is asked about for a project sitepackage and the answer
  leads with a distributed extension's layout. One supported version is a range
  of one, and the sentence was kept true for both — but the hint it now reaches
  is not, and `scope` is the only thing marking that.
- A testing question with no version in it starts leading with the layout hint.
  That is the tie the *test*-carrying patterns were rejected for, and the two
  that stayed are one corpus-growth away from the same score.

## Covered by

- `HintsTest::anAuditAskingAboutTestsReachesTheRuleAboutTheSupportedRange`
- `HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat`
