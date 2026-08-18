# Say where a site nobody wrote came from, and how it differs from the four causes beside it

**Serves:** D-KNW-098, feedback/2026-08-18-074545-the-core-auto-creates-a-site-for-a-new-root.md
**Priority:** normal

Step 1a: nothing says TYPO3 writes a site of its own when a page is created at
the root, so a caller holding one is routed to a collision or an import that is
not there. `D-KNW-098` settles that it is a hint of its own in
`knowledge/hints/project.json`, curated on the provenance rather than on the 404
that `site-base-collision` already claims; what it says is the reading.

Establish against `.checkouts/` whether `12.4` and `13.4` invent the identifier,
the base prefix and the slug reset the way `14.3` does, what
`createNewBasicSite()` writes into the configuration besides the base, and by
what route an extension extends the protected `$allowedPageTypes` — the
`processDatamapClass` key is keyed by class name, which makes a registered
subclass the available one and is what the feedback reports `t3g/blog` doing.

Then make the fifth cause reachable from the four that are written: the
neighbour lines in `site-base-collision` and `initial-content-references`, the
four lookups `typo3-development-installation` names for a request that reached
the wrong site or none, and the cause-per-sentence list in the
`installation-operations` intent.
