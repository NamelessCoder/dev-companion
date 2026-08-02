---
id: R-KNW-30
status: held
---

# R-KNW-30 — A non-English site reaches its label-language setup

**A non-English site setup reaches the complete label-language chain.**

The site language selects the pack key explicitly, the pack is activated through
the configuration key of the target branch before it is updated, and literal
component fallbacks such as the EXT:form submit button are distinguished from
translatable labels.

## From

A German-only site silently rendering core validation messages and the form
submit button in English (2026-07-30).

## Held by

- `HintsTest::aGermanSiteTaskReachesItsLabelLanguageSetup`
- `HintsTest::languagePackActivationUsesTheConfigurationOfTheTargetBranch`
