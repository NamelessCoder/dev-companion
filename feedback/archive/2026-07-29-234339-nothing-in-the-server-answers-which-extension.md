---
date: 2026-07-29T23:43:39+00:00
category: tool-gap
status: closed
closed: 2026-07-30
commit: 4da0471
subject: "[FEATURE] Route extension-point intents through subsystem hints"
tool: typo3_architecture_lookup, typo3_reference_list, typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# Extension-point lookup still cannot route from an intent to a surviving hook

## Observation

The new `form-framework` hint now answers the concrete prefilling task, including
`AfterCurrentPageIsResolvedEvent`, its ordering, and why variants are not the
mechanism. What remains is the general lookup gap: nothing answers "which
extension point do I use for X" when a subsystem still has an `SC_OPTIONS` hook
instead of a PSR-14 event. The surviving hooks are only visible by grepping the
subsystem, and an intent word that is absent from a release changelog cannot
reach them.

## Query

typo3_architecture_lookup id="events-extension-points"

## Suggestion

Name the `SC_OPTIONS` hooks that survived their subsystem's PSR-14 conversion,
then decide whether intent words belong in architecture-hint `appliesTo` or need
a separate lookup. The changelog is organised by release, so it cannot provide
that route.
