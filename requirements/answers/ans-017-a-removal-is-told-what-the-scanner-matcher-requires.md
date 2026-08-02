---
id: R-ANS-017
status: open
restsOn: [D-ANS-029]
---

# R-ANS-017 — A removal is told what the scanner matcher requires

**A caller asking this server about removing public API is told what the
extension scanner matcher requires, in the same answer as the `[!!!]` marker and
the changelog file.**

What the rule says is not fixed here; that is the reading the work opens with.
What this demands is that it arrive on the breaking route rather than only on
the deprecation one. A session reviewing or writing a removal asks about the
removal, and a rule reachable only from the word deprecation is delivered to the
callers who are not making that mistake.

The `breaking` intent of `knowledge/task-intents.json` says "consider an
extension scanner matcher" and is one of the places this could land. Whether a
recommendation is what it should stay is part of the same reading.

## From

`feedback/2026-08-01-115109` (2026-08-01), a review of the core patch replacing
GD-based error thumbnails, which asked `typo3_rule_lookup` for the convention on
removing a public method and had to find the precedent by grepping the checkout.

Measured on 2026-08-02: `typo3_rule_lookup "extension scanner"` returns the
`## Deprecations` section of `knowledge/documents/typo3-commit-messages.md`,
while "breaking change changelog" — the `rulesQuery` the `breaking` intent
itself uses — returns four sections, none of which names a matcher.

## Held by

Not guarded. Nothing asks whether two routes to one subject say the same thing,
and `bin/cli hints:coverage` reads the hint corpus rather than the prose.
