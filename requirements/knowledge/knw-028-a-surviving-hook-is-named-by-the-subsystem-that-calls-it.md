---
id: R-KNW-028
status: held
---

# R-KNW-028 — A surviving hook is named by the subsystem that calls it

**A surviving hook is named by the subsystem that still calls it, alongside the
narrower event for a concrete intent.**

Intent words belong in that hint's `appliesTo`; there is no parallel
extension-point lookup whose registry would duplicate and drift from the
subsystem knowledge.

## From

Prefilling an EXT:form field requiring a grep to discover both a surviving hook
and the request-aware event that should be used instead (2026-07-29).

## Held by

- `HintsTest::survivingHooksAreNamedByTheirSubsystemAndIntent`
