---
date: 2026-07-29T16:18:05+00:00
category: tool-gap
status: closed
closed: 2026-07-29
commit: 03004f0
subject: "[FEATURE] Answer whether an extension is part of the core"
tool: typo3_project_scope, typo3_extension_scope, typo3_catalog_scope
directory: /home/benji/projects/site-new
---

# A WRONG CLAIM I MADE THAT A LOOKUP COULD HAVE PREVENTED.

## Observation

A WRONG CLAIM I MADE THAT A LOOKUP COULD HAVE PREVENTED.

Justifying a records-over-Extbase decision to the user, I wrote that the Record API is "the way the core itself goes — Content Blocks works the same". The user corrected me: "content blocks die extension ist kein core code". Correct — typo3/cms-content-blocks is not a system extension, and citing it as evidence of core direction was wrong. The rest of the argument (record-transformation in 13.4, Record API in the list module and page preview in 14.0, f:render.record in 14.2 — all found through typo3_changelog_lookup) held.

The gap: nothing in this server answers "is X part of the core, and since when". typo3_project_scope lists the extensions of the installation being read and labels them origin=project vs the rest; typo3_extension_scope describes one installed extension; typo3_catalog_scope reports the core revision behind the catalogs. None of them answers the question for an extension that is NOT installed — which is exactly when it matters, because that is when the answer is being taken from memory.

This bites in both directions and I hit both in one session:

- Claiming a community extension is core. Content Blocks here.
- Not knowing that something IS core. typo3/theme-camino is a system extension since v14.1 and I did not know it existed until the user named it — see the separate note. Had I been able to ask "which extensions does the core ship on 14.3", camino would have been in the list and the whole directory-structure rework would not have been necessary.

The installation being read has the answer on disk: the core's own composer.json replaces/requires every system extension, and vendor/typo3/cms-* is enumerable.

## Query

no lookup answers "is this extension part of the core?" — I asserted from memory that Content Blocks is core and was corrected by the user

## Suggestion

Have typo3_project_scope — or a small dedicated lookup — report the system extensions of the TYPO3 line being read, so "is X core" and "what does the core ship" are answerable rather than recalled. Reading the core package's composer.json replace list per major would also let the answer carry a "since" version, which is what makes an entry like theme_camino (v14.1) useful. Marking the currently installed ones separately would keep the existing typo3_project_scope output intact.
