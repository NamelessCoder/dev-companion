---
id: R-ANS-015
status: open
restsOn: [D-ANS-024]
---

# R-ANS-015 — A label rule reaches the task that never names a label

**A task that will add an XLF trans-unit is told the source language of the file
it writes into, without having named labels, XLF or translation in its own
words.**

The rule itself is `R-KNW-033` and is not restated here. What this demands is
that it arrive, and it does not today: every route to it opens on vocabulary the
caller has to supply first. A session describing its work as the work — a
content element, a backend module, a plugin — is describing something that will
need labels, and that is the point at which the source language is still cheap
to get right.

Naming labels is what a caller does once it already knows they are a subject
with rules of their own. A rule reachable only from there is delivered to the
sessions that would not have broken it.

## From

`feedback/2026-08-01-003313` (2026-08-01), a TYPO3 14 testimonials session in
`/home/benji/projects/site-new` that added `backend_fields.xlf` and
`messages.xlf` units in German because the site is German-only, and
`feedback/archive/2026-07-30-185539` before it, a forward run of EXT-04 that did
the same in a sitepackage whose existing XLF files were German. Between the two,
the rule landed and the second report arrived anyway.

Measured on 2026-08-02:
`bin/cli hints:probe "add a testimonials content element to the sitepackage"`
reaches `content-elements`, `sitepackage-layout` and `frontend-page-rendering`.
Adding the word labels to the same query is what brings `language-files` back.

## Held by

Not guarded. Nothing today asks a query to reach a hint it does not name, and
which answer carries the rule is open — `bin/cli hints:coverage` reports what a
scenario prompt reaches, but its prompts are not this shape.
