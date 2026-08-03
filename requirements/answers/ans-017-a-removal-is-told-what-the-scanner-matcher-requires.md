---
id: R-ANS-017
status: held
restsOn: [D-ANS-035]
---

# R-ANS-017 — A removal is told what the scanner matcher requires

**A caller asking this server about removing public API is told what the
extension scanner matcher requires, in the same answer as the `[!!!]` marker and
the changelog file.**

A rule reachable only from the word deprecation is delivered to the callers who
are not making that mistake. A session reviewing or writing a removal asks about
the removal.

What the rule says was settled against `.checkouts/main` and is
[`D-ANS-035`](../../decisions/answers/ans-035-the-matcher-entry-is-owed-to-what-the-changelog-tag-claims.md):
the entry is owed to what the changelog entry's scanned tag claims, which is why
the `breaking` intent states it rather than recommending it.

## From

`feedback/2026-08-01-115109` (2026-08-01), a review of the core patch replacing
GD-based error thumbnails, which asked `typo3_rule_lookup` for the convention on
removing a public method and had to find the precedent by grepping the checkout.

Measured on 2026-08-02: `typo3_rule_lookup "extension scanner"` returned the
`## Deprecations` section of `knowledge/documents/typo3-commit-messages.md`,
while "breaking change changelog" — the `rulesQuery` the `breaking` intent
itself uses — returned four sections, none of which named a matcher.

## Held by

- `KnowledgeTest::theBreakingRouteStatesWhatTheScannerMatcherRequires`
- `HintsTest::aRemovalIsToldWhatTheScannerMatcherRequires`
