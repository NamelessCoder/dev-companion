---
id: R-KNW-036
title: 'Label reuse stays at the usage context'
status: held
heldBy:
  - HintsTest::labelReuseStaysAtTheUsageContext
  - LabelSearchTest::aResourceRestrictsReuseToTheUsageContext
---

# R-KNW-036 — Label reuse stays at the usage context

**Label reuse is local to the translation resource and semantic context already
used at the consuming code.**

An identical string elsewhere in the installation is not a cross-module
vocabulary, and a new unit id names its concrete use within the local resource
rather than using a context-free word such as `new`.

## From

A label search whose installation-wide matches looked reusable even though they
belonged to unrelated modules and packages (2026-07-30).
