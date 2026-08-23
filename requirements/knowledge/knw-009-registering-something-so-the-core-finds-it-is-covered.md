---
id: R-KNW-009
title: 'Registering something so the core finds it is covered'
status: held
heldBy:
  - HintsTest::registeringSomethingSoTheCoreFindsItIsCovered
---

# R-KNW-009 — Registering something so the core finds it is covered

**"How do I register this so the core finds it" is a covered question.**

Registering a content type, and registering a class the container resolves by
name, both fail at request time and neither is a convention of a subsystem or a
piece of backend markup — the two places an answer was looked for.

## From

A content element registered with a call signature from the previous major, and
a page title provider that was not public and therefore not found (2026-07-29).
