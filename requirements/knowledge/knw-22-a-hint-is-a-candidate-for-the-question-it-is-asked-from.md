---
id: R-KNW-22
status: held
---

# R-KNW-22 — A hint is a candidate for the question it is asked from

**A hint is a candidate for the question its subject is asked from, not only
for the one its category is named after.**

Domains withhold whole categories before anything is scored, so a category
whose vocabulary is the vocabulary of somebody who already knows the answer is
invisible: the words a caller arrives with are what they can see — a colour,
a dark mode, a shadow, a spacing — and the words the hints were filed under
are `sass`, `scss`, `css`. A hint that its own title does not reach is
unreachable, and that is the floor this holds. It is
[`R-KNW-13`](knw-13-a-statement-lives-in-the-category-it-is-asked-from.md) at
the gate rather than in the filing, and it does not widen what is answered:
`namesTheFrontend` still withholds the backend's own design system where the
task is about the website. A component asked for by name is
`typo3_component_lookup`'s and stays there.

## From

The first `bin/cli hints:coverage` reading (2026-07-30) — eight of the nineteen
Backend CSS hints not reached by their own title, and all nineteen unreached by
every scenario prompt.

## Held by

- `HintsTest::everyHintIsReachedByItsOwnTitle`
- `HintsTest::whatACallerCanSeeReachesTheHintAboutIt`, with
- `HintsTest::aPhpPathIsNeverAnsweredWithFrontendConventions` and
- `ScopeTest`'s frontend withholding holding the other direction
