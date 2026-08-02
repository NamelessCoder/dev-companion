---
id: D-KNW-009
date: 2026-08-02
status: open
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
  `sitepackage-layout` to `sitepackage-initial-content` ahead of it, reaching
  no testing hint either way. Term weights are taken over the candidates
  (`R-ANS-007`), so widening the candidate set reweighs every term in it, and a
  keyword that only widens buys a different wrong answer.
- The phrasings do move it, once `project-extension-tests` also carries
  `test coverage` in its `appliesTo`: `SKILL-01` leads with that hint, and so
  does the query this started from, with or without a path.
- What none of them moves: "test the frontend rendering of the page",
  "browser tests for the site package", "add tests for the DataHandler change"
  and the three other phrasings `HintsTest` holds.

## Decided

- The domain vocabulary and the hint vocabulary are widened together. A domain
  makes a category a candidate; what wins inside it is the hint's own
  `appliesTo`, and moving one without the other trades a miss for a wrong hit.
- No bare `test`. `Text::containsWord()` matches at a word boundary as a
  prefix, so `test` would also carry "testing" and "tests" — the reach is not
  the problem, the reweighing is.

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

## Covered by

- `HintsTest::settingTestsUpInAPackageReachesTheHintAboutThat`
