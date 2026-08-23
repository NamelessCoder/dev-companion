---
id: D-SKL-019
title: An absent surface is asked for by the id of its convention
date: 2026-08-04
status: open
---

# D-SKL-019 — An absent surface is asked for by the id of its convention

**A conformance surface the checkout has no files for is asked with the hint id
that owns it, and the checklist writes that id beside the surface.**

An audit published "no manual needed" for a project sitepackage without ever
asking the convention that says an extension has one.

## Evidence

- `feedback/2026-08-04-175935`. `typo3_extension_describe` answered
  `manual: null, readme: null`, the skill's own "absence of an optional
  subsystem is not a defect" did the rest, and the maintainer found the gap
  afterwards. The convention is not silent: `extension-documentation` says a
  manual lives in `Documentation/` with `Index.rst` and `guides.xml`.
- The skill prescribes the query form that could not be composed here: "with the
  subsystem's concrete paths and a short English description". A surface with no
  files has no path, which is exactly the surface whose absence is the finding —
  the same page says so three paragraphs earlier, about deriving the list from
  the checklist rather than from `find`.
- The surfaces whose convention did get asked are the ones whose id is written
  where the session was working. `static-quality.md` names
  `extension-static-analysis` and `extension-coding-standards` inline and both
  were called; the Quality row named documentation as a bare noun.

## Decided

- The judgement is **step 3**, routing, and it is **closed on the spot**: a
  routing line onto a hint that exists, with nothing about TYPO3 looked up.
- Both halves are written. The Quality row carries `id=extension-documentation`,
  and the method bullet says that a surface with no files is asked by its id
  rather than by its paths.
- The other bare nouns in that row stay as they are. Which hint owns "the test
  suite" or "upgrade readiness" for an extension audit is a reading of the
  corpus rather than a rename, and one wrong id in a published skill is worse
  than a noun.

## Assumed

- That the id in the row is what gets it asked. It is the difference the
  reporting session names between the surfaces it asked and the one it did not,
  and it is one session.

## Wrong if

- Another bare noun in that row produces the same miss. Then the row needs every
  id rather than the one that failed, and the reading above was deferred too
  long.
- A session asks the hint by id, gets the convention, and still reports absence
  as clean. Then the lever is the skill's "absence is not a defect" sentence
  rather than the lookup.

## Since then

The row still carries the id and it survived a merge. `8a67338a` on 2026-08-19
made the audit and its fixes one skill, and
`skills/typo3-extension-health/references/checklist.md` opens the Quality row
with documentation and the lookup beside it — `typo3_hint_lookup` with
`id=extension-documentation`, "which says what a manual consists of and that it
ships with the package". It is the only id the checklist names.

So the first **Wrong if** is standing rather than fired. Four nouns in that same
row are still bare — the test suite, the check layer, deprecations and upgrade
readiness — and the check layer is the one that has an answer elsewhere, in the
section below it that names each entry by what it establishes. Nothing reports a
second miss, which is what the bullet waits for; what it says then is that the
row needs every id rather than the one that failed.

The second one has no report either. No feedback since names a session that
asked `extension-documentation` by id and still published an absence as clean.
