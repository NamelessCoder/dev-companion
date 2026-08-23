---
id: R-KNW-011
title: 'Extbase is covered as its own subject'
status: held
heldBy:
  - HintsTest::anExtbasePluginHasAHintOfItsOwn
---

# R-KNW-011 — Extbase is covered as its own subject

**Extbase is covered as its own subject, including what breaks while writing a
plugin.**

Those are the cache hash of a GET form, the property mapping of an object
argument, an unpersisted argument dropped from a link, a paginator clamping an
out-of-range page, and the routes a paginated plugin needs.

Each of them answers with a wrong page or an error page rather than with a stack
trace anyone could search for.

## From

A catalog with fifty hint ids and not one about Extbase, and the five failure
modes met afterwards while building the plugin it had nothing to say about
(2026-07-29).
