---
id: R-KNW-031
title: 'A persisted alias is answered in both directions'
status: held
---

# R-KNW-031 — A persisted alias is answered in both directions

**A PersistedAliasMapper answer states both directions: link generation takes a
record uid and emits the configured route-field value, while route matching
resolves that value back to the uid.**

It also states the consequences that make the design useful: site-unique values,
rejection of unmatched paths before rendering, and no cHash for the mapped
argument.

## From

An implementation passing and validating the display value as a query argument
because the mapper's direction was left implicit (2026-07-30).

## Held by

- `HintsTest::persistedAliasesStateBothDirectionsAndTheirValidationBoundary`
