---
date: 2026-07-30T18:55:41+02:00
category: wrong-answer
status: open
tool: typo3_catalog_scope, typo3_component_lookup
---

# The catalog's provenance overrides the version binding on its entries.

## Observation

The catalog's provenance overrides the version binding on its entries.
typo3_catalog_scope announces one branch for the whole catalog — "Branch: main
(TYPO3 15.0)" — while every entry carries the majors it was verified on and
targetVersion already withholds what does not hold there.

In a forward run of EXT-04 against a TYPO3 14.3.5 installation, the session
called typo3_component_lookup four times with targetVersion 14.3, then set the
answers aside: "I verified each class and the custom element against the 14.3.5
install rather than the component catalog, which is cut from 15.0." It re-derived
the markup with about twenty-five greps through vendor/typo3. The entries it
distrusted say "Verified on: TYPO3 v13 and newer" and were valid for that
installation.

The provenance line is true and it is also the first thing read, so it is taken
as the version of every answer under it.

## Query

badge, table and status indicator markup for TYPO3 14.3

## Suggestion

Say what the branch is: the checkout the entries were read from, not the version
they hold for. Put the validity where the answer is — an entry filtered by
targetVersion already carries it — and have typo3_catalog_scope state that
filtering happens, so a caller has no reason to re-derive what it was just
handed.
