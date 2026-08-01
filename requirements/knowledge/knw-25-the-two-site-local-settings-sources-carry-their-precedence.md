---
id: R-KNW-25
status: held
---

# R-KNW-25 — The two site-local settings sources carry their precedence

**The two site-local settings sources are answered with their precedence.**

`config/sites/<identifier>/settings.yaml` replaces the inline `settings:` block
of `config.yaml` rather than merging with it, and the backend editor persists to
the former.

**From:** adding one setting in the backend silently dropping every value a
sitepackage seed carried inline (2026-07-30).

**Held by:**
`HintsTest::siteLocalSettingsSourcesAreAnsweredWithTheirPrecedence`
