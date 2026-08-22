---
id: D-KNW-009
title: A domain keyword is a phrasing, not a word
date: 2026-08-02
status: open
coveredBy:
  - HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat
---

# D-KNW-009 — A domain keyword is a phrasing, not a word

**Testing reaches the PHP domain through phrasings, not through the bare word
`test`.**

The five are `test coverage`, `test suite`, `set up tests`, `write tests` and
`automated test` — what somebody with no suite yet says.

`Domains::KEYWORDS[PHP]` carried `unit test` and `functional test` and nothing
else, so a question about testing that had not yet reached a harness — "set up
tests for our site package extension" — was Fluid and TypoScript work, and every
hint in `php.json` was filtered out before anything was scored.

## Evidence

- Measured over the 105 texts this repository has to hand: 40 scenario prompts
  and 65 hint titles. Nine carry the word "test" and two of them would change
  domain by it — `SKILL-01` and `SKILL-04`.
- The bare word was tried first and is worse than the gap. `SKILL-04` does not
  move, and `SKILL-01` — "Review our site package's test coverage" — went from
  `sitepackage-layout` to `sitepackage-initial-content` ahead of it, reaching no
  testing hint either way. Term weights are taken over the candidates
  (`R-ANS-007`), so widening the candidate set reweighs every term in it, and a
  keyword that only widens buys a different wrong answer.
- The phrasings do move it, once `project-extension-tests` also carries
  `test coverage` in its `appliesTo`: `SKILL-01` leads with that hint, and so
  does the query this started from, with or without a path.
- What none of them moves: "test the frontend rendering of the page", "browser
  tests for the site package", "add tests for the DataHandler change" and the
  three other phrasings `HintsTest` holds.

## Decided

- The domain vocabulary and the hint vocabulary are widened together. A domain
  makes a category a candidate; what wins inside it is the hint's own
  `appliesTo`, and moving one without the other trades a miss for a wrong hit.
- No bare `test`. `Text::containsWord()` matches at a word boundary as a prefix,
  so `test` would also carry "testing" and "tests" — the reach is not the
  problem, the reweighing is.

## Assumed

- A caller who has no suite yet says one of these five. It is the vocabulary of
  the case the domain was missing rather than of the case it already had, and
  nothing measures the phrasings nobody in this repository has written down.

## Wrong if

- A question about testing still lands in Fluid because it uses a sixth
  phrasing. The answer is then another entry rather than the bare word, and the
  measurement above is the one to repeat.
- The PHP category starts winning where the question is about the website —
  which is what the three unmoved phrasings above are for, and what a fourth
  keyword would put at risk.

## Since then

The sixth phrasing arrived, and it came out of this repository's own text.
`typo3-extension-conformance`'s checklist writes its audit surfaces down, and
the quality one reads "Quality: tests, the check layer, documentation,
deprecations, and upgrade readiness". Its word for the surface is the bare
`tests` this entry rejected. So an audit asking in the checklist's own wording
reaches no PHP hint at all:
`bin/cli hints:probe "audit the quality surface of an extension: tests, the check layer, deprecations and upgrade readiness"`
resolves to the `docs` domain alone and returns `deprecated-apis` and
`installation-upgrade`.

What that cost is on the record. The feedback of 2026-07-31 19:36 UTC reports a
conformance audit whose recommendation 19 was to "Consider typo3/cms-
compatibility package for cross-version testing". No covered line ships such a
package — no `compatibility` sysext under `.checkouts/12.4` or `13.4`, and no
occurrence of the name anywhere in the checkouts — and
`typo3_system_extension_lookup` says so outright when asked. The corpus already
holds what the audit should have said, in `extension-repository-layout`: a
matrix that resolves per supported version, the lowest and the highest of each
supported major.

The hint half of **Decided** is the second place it did not reach, and this one
is inside the PHP domain rather than in front of it. Testing vocabulary reaches
`project-extension-tests`, whose `appliesTo` carries ten testing phrasings and
whose statements say nothing about a supported range.
`extension-repository-layout` carries the range and no testing phrasing, so it
scores on body text or not at all. "does the test suite cover every supported
TYPO3 version" ranks the harness hint first at `appliesTo(10) + text(173)`, over
`text only(209)`; "test the extension on TYPO3 12 and 13" puts it seventh of
ten. Asked through the tool the skill actually calls, the split is total:
`typo3_hint_lookup` for an extension's quality surface returns
`installation-upgrade`, `deprecated-apis` and `project-repository-layout` — the
last of them `scope: project`, which is not even the unit under audit.

Both reaches are settled in
[`D-KNW-013`](knw-013-this-repositorys-own-sentence-is-reworded-rather-than-indexed.md),
which repeated this measurement over the 107 texts the repository has to hand
now. The sixth phrasing is not a keyword: the checklist sentence was reworded to
name the test suite and the supported versions it runs on, and the hint half is
two patterns on `extension-repository-layout` that name a version rather than a
test. The bare word was measured again and rejected again, and a second copy of
the range rule in `project-extension-tests` moved nothing. What this entry
decided holds — the two vocabularies were widened together, and neither half
reaches the rule without the other.
