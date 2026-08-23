---
id: R-KNW-015
title: Building the test harness is covered as its own subject
status: held
heldBy:
  - HintsTest::aProjectExtensionIsToldHowToGetASuiteAtAll
---

# R-KNW-015 — Building the test harness is covered as its own subject

**Where a core answer assumes a harness the core already has, building that
harness is covered as its own subject.**

The conventions of a core test transfer to a project extension unchanged;
everything between `composer require` and the first green run does not exist
there and is the larger half of the work.

## From

A session that took `core-tests` into a project and paid for the phpunit
boilerplate, the database credentials, the document-root-relative extension
paths, the missing `SiteBasedTestTrait` and a `sys_template` that silently
dropped the site set TypoScript (2026-07-29).
