---
id: R-KNW-003
status: held
---

# R-KNW-003 — A hint says how a subsystem is used

**A hint says how a subsystem is used, not only what a patch to it has to
satisfy.**

Both audiences read the same entry: "DataHandler changes are high-impact and
usually need functional tests" is true and answers nobody's question about how
to write a datamap. Where a mechanism has a shape that is easy to get wrong — an
order, a naming rule, a step that happens at install time — the hint states it.

## From

A session that built a site with this server as its only reference and found the
catalog organised around "what must a patch satisfy to be merged" while the
questions were "how do I do X with this API" (2026-07-29).

## Held by

- `HintsTest::theFrontendRenderingPathIsAnsweredAsWellAsTheBackendOne`, and the
  shape of `datahandler-persistence`, `sitepackage-initial-content`,
  `public-assets`, `frontend-page-rendering` — not guarded beyond that.
