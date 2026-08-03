---
id: R-KNW-039
status: held
---

# R-KNW-039 — A backend module in a sitepackage stays backend-module work

**A backend module owned by a sitepackage remains backend-module work.**

Its domains include PHP, its registration hint ranks first, and generic mentions
of `sitepackage` and `records` do not pull in the package-layout or
initial-content or frontend-record-rendering guides. Those guides apply only
when the task asks for that layout, content shipping or frontend rendering, not
when the words merely name the owner and the data a backend module reviews.

## From

A backend review module with actions, badges, icons and translated labels whose
17 KB guide was dominated by frontend records and the complete sitepackage
layout while omitting PHP/module registration (2026-07-30).

## Held by

- `HintsTest::aBackendModuleInASitepackageDoesNotBecomeFrontendWork`
