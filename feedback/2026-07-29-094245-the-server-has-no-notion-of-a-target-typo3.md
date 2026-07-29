---
date: 2026-07-29T09:42:45+00:00
category: wrong-answer
status: open
tool: typo3_component_lookup, typo3_architecture_lookup
---

# There is still no way to name the version an answer has to hold for

## Observation

Partly addressed. The server now reads the TYPO3 version of the installation it
was started in, contrasts it with the catalog pin in every answer that carries
the pin, and refuses to hand a translation domain to an installation that has
none — which was the dangerous case in this note.

Two gaps are left. A caller working on a version other than the one the
discovered installation runs — a backport branch, a second checkout, an
extension supporting 13.4 and 14.3 at once — has no way to say so, and gets the
installation's version taken for theirs. And where there is no installation at
all, nothing is qualified: the catalogs answer as timeless fact again.

The architecture hints held up by design, since `AGENTS.md` requires them to be
branch-neutral, so this is about the catalogs and the component markup.

## Query

typo3_component_lookup{query:"content element backend preview"} from an extension supporting TYPO3 13.4 and 14.3

## Suggestion

Accept an optional `targetVersion` on the version-sensitive tools and let it win
over the discovered installation: `typo3_component_lookup{query:..., targetVersion:"13.4"}`
either qualifies the answer or declines it. `typo3_server_scope` should say that
callers on an LTS branch are expected to pass it. Where neither a target version
nor an installation is known, say that the answer describes the snapshot and
that nothing was compared with it.
