---
date: 2026-07-29T09:42:45+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 9208803
subject: "[FEATURE] Let a target version decide in the component catalog"
tool: typo3_component_lookup, typo3_architecture_lookup
---

# There is still no way to name the version an answer has to hold for

## Observation

Partly addressed. The server now reads the TYPO3 version of the installation it
was started in, contrasts it with the catalog pin in every answer that carries
the pin, and refuses to hand a translation domain to an installation that has
none — which was the dangerous case in this note.

One gap is left, and it is the catalogs. `typo3_architecture_lookup` and
`typo3_task_guide` now take `targetVersion` and leave out what does not hold
there; `typo3_component_lookup` and `typo3_catalog_scope` do not. They still
answer from one pinned revision, and all they say about a caller on another line
is the skew sentence — which names the difference without acting on it.

The architecture hints held up by design, since `AGENTS.md` requires them to be
branch-neutral, so this is about the catalogs and the component markup.

## Query

typo3_component_lookup{query:"content element backend preview"} from an extension supporting TYPO3 13.4 and 14.3

## Suggestion

Accept `targetVersion` on the catalog tools too, and let it decide rather than
only inform: markup taken from one revision either holds on the stated version
or it does not, and the honest answer for "does not" is to decline it and name
what to verify against. The component catalog would have to record what it was
verified against per entry for that — which is the same `since`/`until` model
the hints already use.
