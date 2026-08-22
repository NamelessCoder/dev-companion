---
id: D-ANS-040
title: A boundary guard is asked with a query that clears the coverage floor
date: 2026-08-03
status: open
coveredBy:
  - ScopeTest::whatARuleAnswerWithheldIsNamedRatherThanMissing
---

# D-ANS-040 — A boundary guard is asked with a query that clears the coverage floor

**The test holding that a rule answer names what it withheld asks a query whose
section clears the coverage floor by a margin, rather than one sitting on it.**

Coverage is a share of the query's weight, and a term is weighed by how few
sections carry it. So every section added anywhere in the corpus moves every
query's arithmetic, and a fixture that passes at a hundredth above the floor is
one entry away from failing on a corpus that got better.

## Evidence

- `whatARuleAnswerWithheldIsNamedRatherThanMissing` asked
  `review readiness for my site package`, and the section it depends on —
  `Review Readiness`, withheld outside the core — covered 0.508 of that query
  against a floor of 0.5. Eight thousandths.
- Four sections about the Gerrit push were added to `typo3-gerrit-workflow` for
  `R-KNW-057`, and the same section fell to 0.462. Nothing about it changed: the
  new sections say `reviewers`, `Under Review` and `git-review`, so `review`
  went from 1.02 to 0.88 and `readiness` from 2.53 to 2.27, while `site` and
  `package` — which that section never carried — rose with the corpus.
- Holding the old query green needed the new prose to give up the two words that
  are its subject: the tracker status `"Under Review"` and the name of the
  `git-review` tool that reads `.gitreview`. Measured, that lands at 0.505 —
  eight thousandths again, in the other direction.
- `code style rules for my site package` reaches `Code Style` in the same
  withheld document at 0.612, and returns two kept sections from
  `typo3-commit-messages` beside it. It exercises the same path: a hit outside
  the core with a core-only document left out and named.
- The inside-the-core sibling, `insideTheCoreARuleAnswerWithholdsNothing`, is
  not on the floor — its query reaches `Review Readiness` at 0.666 — and is
  untouched.

## Decided

- The withholding guard asks `code style rules for my site package`.
- The knowledge prose keeps the words its subject is written in. A corpus edited
  to keep a fixture above the floor answers worse for every caller and would
  have to be edited again by the next entry.
- The margin is what makes a fixture a guard, so the docblock carries the
  measurement rather than only the query.

## Assumed

- That the test is about the shape of the answer — a hit outside the core says
  which core-only document it left out — and not about that one query. The
  docblock says the first.
- That the query moved off is not itself the finding.
  `review readiness for my site package` outside the core now returns the
  commit-message sections with nothing named as withheld, which is a thinner
  answer than it gave before, and no feedback has reported it.

## Wrong if

- A session outside the core asks about review readiness, is answered from the
  commit conventions alone, and concludes this server has nothing on the
  subject. Then the floor is what needs the attention, not the fixture.
- The new query drifts under the floor as the corpus grows. Then a fixture is
  the wrong instrument for this and the guard has to be built out of what the
  answer contains rather than out of what a query happens to weigh.
