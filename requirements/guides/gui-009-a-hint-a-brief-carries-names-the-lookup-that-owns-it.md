---
id: R-GUI-009
status: held
restsOn: [D-GUI-007]
---

# R-GUI-009 — A hint a brief carries names the lookup that owns it

**Where `typo3_task_guide` carries hints, it says they are
`typo3_hint_lookup`'s and that it carries the strongest few of them.**

A guide that quotes another tool's corpus is the only place a reader can learn
whose the rule is. Without the sentence a report either cites the tool that did
not answer, which sends the next reader to an empty call, or cites the guide,
which is not where the rule lives. The second half is owed for the same reason:
a brief takes four per group of paths, and a caller who reads that as the
subsystem's conventions has stopped one call short of them.

## From

The fifth recorded `REVIEW-03` run, which quoted `fluid-viewhelpers` and
`core-tests` as `typo3_hint_lookup` and never called it — correctly attributed,
because `typo3_task_guide` had returned exactly those records, and citable
nowhere the reader could follow (2026-08-03, `D-SKL-009`).

## Held by

- `HintsTest::theHintsABriefCarriesNameTheLookupTheyCameFrom`
