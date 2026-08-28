---
id: D-VER-002
title: The prose is not bound; it says which half it is
date: 2026-07-29
status: revoked
revokedBy: D-VER-005
coveredBy: []
---

# D-VER-002 — The prose is not bound; it says which half it is

**The prose carries no version binding and says so in every answer, naming
`typo3_hint_lookup` with `targetVersion` as where the bound form is.**

The architecture hints now carry `since`/`until` on every statement that changed
inside the covered range. The markdown documents below `knowledge/` are the long
form of the same subjects and carry nothing — the event listener attribute, the
Fluid file extension, the backend tokens and the translation domains are all
described there as the shape, with no range.

## Decided

- No binding mechanism for prose. It would need per-bullet metadata in markdown,
  a parser, a renderer and a test, for a corpus that is read whole rather than
  filtered, and the same statements are already bound where a caller acts on
  them.
- Every prose answer says so instead, in one sentence from
  `Tools::renderSections()`, and names `typo3_hint_lookup` with `targetVersion`
  as where the bound form is. One sentence in one place, so a caller who learns
  it in a rule answer finds it unchanged in a script answer.

## Assumed

- A caller that is told the prose is unfiltered will ask the hints when the
  version matters. The alternative — filtering prose sections by the ranges of
  the hints that share their subject — would guess at a mapping nobody declared.

## Wrong if

- A prose section misleads on an LTS badly enough that the sentence does not
  save it, which would mean that statement belongs in the hints rather than in
  the document.

## Confirmed on 2026-08-02

The **Wrong if** fired: `typo3-core-scripts.md` handed a 12.4 contributor three
suites that branch does not have, and the section is returned whole and
unfiltered. The decision holds because its own remedy absorbed all of them — the
range for a command lives on the suite, so the sections lost the commands and
gained a pointer. What did not hold is the line every rendered section opens
with, which named the hint lookup alone; it names the test run guide for a
command now. Prose that describes a shape rather than a command is still read
rather than run.

## Revoked on 2026-08-04

The class this entry left open arrived as a section whose body is a file the
caller writes out verbatim. Both properties this rested on fail there: the
document is where the caller acts rather than a long form of something bound
elsewhere, and there is no hint carrying the file for the range to sit on. The
remedy that absorbed the **Wrong if** in August cannot be applied either — a
command could be taken out of the prose because its range already lived on a
suite, and a file has nowhere to be taken out to.

What holds instead is
[`D-VER-005`](ver-005-a-document-section-declares-the-majors-it-holds-for.md),
whose **Wrong if** is a different list: what can go wrong now is a variant
handed over with nothing saying which to write, not a section that misleads
because nothing could filter it.
