# Say what the component index is a subset of, before it is called

**Serves:** feedback/2026-08-10-182511-component-lookup-was-passed-over-while.md
**Priority:** normal
**Branch:** todo/say-what-the-component-index-is-a-subset-of
**Claimed:** 2026-08-11

Say in `ComponentLookup::description()` that the index is a curated subset of
what the core files as a component — the partials under
`Build/Sources/Sass/component/` and the custom elements under `element/` — so a
caller reads the boundary before it decides whether to call rather than in the
miss text afterwards. The `routing` entry in `knowledge/server-scope.json` at
"About to write backend markup or invent a CSS class name" and
`scope.components` in `knowledge/catalog/meta.json` state the same boundary and
are rewritten with it. `bin/cli tools:index` regenerates the reference under
`documentation/tools/`, which `tools:check` holds to the registry. `D-CAT-004`
carries the boundary and why the examples in the description were read as one.
The feedback stays open while another todo still serves it —
`bin/cli feedback:list` says which.
