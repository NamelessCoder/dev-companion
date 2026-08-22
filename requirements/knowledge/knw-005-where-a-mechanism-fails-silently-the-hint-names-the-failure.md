---
id: R-KNW-005
title: 'Where a mechanism fails silently, the hint names the failure'
status: held
---

# R-KNW-005 — Where a mechanism fails silently, the hint names the failure

**Where a mechanism fails silently, the hint names the failure, not only the
rule.**

A caller whose page comes back wrong with a 200 and an empty log has nothing to
search for, so the sentence worth writing down is the one that says what it
looks like when it goes wrong.

## From

A variable assigned outside `<f:section>` in a template that declares a layout,
never executed and never reported; an HTML comment whose `{placeholders}` were
resolved into the response; a layout root that put the page frame inside every
content element; and `excludeDoktypes` replacing the default list so that every
storage folder appeared in the menu (2026-07-29).

## Held by

- `HintsTest::theTemplateTrapsThatFailWithoutAnErrorAreNamed`
- `HintsTest::aNavigationIsAnsweredWhereMenusAreActuallyConfigured`
- `HintsTest::anAssetThatNeverReachesThePageIsAnsweredByItsLayout`
  — the other half: naming the failure is what makes the hint reachable by it
