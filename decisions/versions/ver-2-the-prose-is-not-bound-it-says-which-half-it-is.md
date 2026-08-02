---
id: D-VER-2
date: 2026-07-29
status: open
---

# D-VER-2 — The prose is not bound; it says which half it is

**The prose carries no version binding and says so in every answer, naming
`typo3_architecture_lookup` with `targetVersion` as where the bound form is.**

The architecture hints now carry `since`/`until` on every statement that
changed inside the covered range. The markdown documents below `knowledge/` are
the long form of the same subjects and carry nothing — the event listener
attribute, the Fluid file extension, the backend tokens and the translation
domains are all described there as the shape, with no range.

## Decided

- No binding mechanism for prose. It would need per-bullet metadata in
  markdown, a parser, a renderer and a test, for a corpus that is read whole
  rather than filtered, and the same statements are already bound where a
  caller acts on them.
- Every prose answer says so instead, in one sentence from
  `Tools::renderSections()`, and names `typo3_architecture_lookup` with
  `targetVersion` as where the bound form is. One sentence in one place, so a
  caller who learns it in a rule answer finds it unchanged in a script answer.

## Assumed

- A caller that is told the prose is unfiltered will ask the hints when the
  version matters. The alternative — filtering prose sections by the ranges of
  the hints that share their subject — would guess at a mapping nobody
  declared.

## Wrong if

- A prose section misleads on an LTS badly enough that the sentence does not
  save it, which would mean that statement belongs in the hints rather than in
  the document.
