# Settle whether "content element" still has to be a Fluid and TypoScript keyword

**Serves:** decisions/
**Run:** `bin/cli hints:probe "Add a TCA field to the content element in the backend"`

`D-KNW-1` was corrected on 2026-08-02: the hint half is answered, the domain
half is not. A backend-only task naming a content element no longer comes back
with `sitepackage-layout`, because `ArchitectureHints::find()` now excludes it
for a backend-only task — but the brief still reports `Domains: fluid,
typoscript`, because `Domains::KEYWORDS` lists "content element" under both.
That was the second **Decided** bullet of `D-KNW-1` and nothing has revisited
it. The step is to find out what those two domains still buy: run the probe
above and the same query with the keyword removed from `FLUID` and `TYPOSCRIPT`,
and read what changes across the scenario prompts with the script `SITE-05` and
`SKILL-04` describe — a content element that is built and rendered has to keep
reaching `frontend-page-rendering` and the site set hints. Where removing it
costs those, the keyword stays and the domains line is what needs a `binding`
rather than a deletion. `SITE-08` is where the outcome is written down; its
`not guarded` half is exactly this.
