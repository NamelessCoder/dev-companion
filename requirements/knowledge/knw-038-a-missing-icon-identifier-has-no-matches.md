---
id: R-KNW-038
status: held
---

# R-KNW-038 — A missing icon identifier has no matches

**A complete backend icon identifier is validated exactly.**

A missing identifier has `matchCount: 0` in structured data even when related
identifiers are offered, and those carry a separate `suggestionCount`. Leading
categories such as `actions-` and `content-` describe the icon's usage and do
not by themselves make every icon in that category a match or a suggestion.

## From

`actions-definitely-does-not-exist` correctly described as missing in text
while its structured answer claimed 556 matches from the `actions-` prefix
(2026-07-30).

## Held by

- `IconLookupTest::aMissingIdentifierHasNoMatchesEvenWhenRelatedIconsExist`
