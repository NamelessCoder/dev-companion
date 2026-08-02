---
id: D-KNW-4
date: 2026-07-30
status: open
---

# D-KNW-4 — Package knowledge needs a producer before it needs discovery

**A package contribution is reported with the package and version Composer
states and an authority of its own.**

No discovery path is added until a real producer has established a shape.

An installed extension can eventually contribute task guidance for its own API
and workflow, but loading an arbitrary markdown path from every package would
erase the distinction this server already makes between a core rule, a
transferable convention and somebody else's package advice.

## Decided

- A package contribution, when one exists, is reported with `package` and
  `packageVersion` derived from Composer rather than trusted from its file,
  `authority: "package"`, and `appliesTo` fixed to that Composer package by
  default. A package may augment its own namespace. An explicit override target
  is valid only below the same package authority; matching the path or name of
  a bundled core convention never overrides it.
- No discovery path or general loader is added yet. There is one canonical
  package skill in this repository and no real third-party producer whose
  layout, update cycle or override need has established a common shape. A
  fixture written now would prove only that code can read the format it just
  invented.

## Assumed

- The first useful contribution will be procedural task guidance, not
  replacement facts about TYPO3 itself. Facts about a package remain its
  documentation; facts about TYPO3 remain versioned live documentation or the
  curated corpus.

## Wrong if

- One real extension is ready to ship agent material. Add its scenario first,
  record the package and version in every answer, then implement the narrowest
  discovery path its package can actually publish. A second producer is what
  justifies extracting a shared format and override rules.

## Since then

The maintainer settled on 2026-08-02 that no third-party package contributes
data here for now. From here the missing discovery path is a scope choice
rather than a wait, so **Wrong if** narrows: a producer being ready is no
longer enough on its own. The todo that carried the question is gone with it,
and this entry is where the question is kept.
