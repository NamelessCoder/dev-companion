---
id: R-KNW-11
status: held
---

# R-KNW-11 — Extbase is covered as its own subject

**Extbase is covered as its own subject, including what breaks while writing a
plugin: the cache hash of a GET form, the property mapping of an object
argument, an unpersisted argument dropped from a link, a paginator clamping an
out-of-range page, and the routes a paginated plugin needs.**

Each of them answers with a wrong page or an error page rather than with a
stack trace anyone could search for.

**From:** a catalog with fifty hint ids and not one about Extbase, and the five
failure modes met afterwards while building the plugin it had nothing to say
about (2026-07-29).

**Held by:** `HintsTest::anExtbasePluginHasAHintOfItsOwn`
