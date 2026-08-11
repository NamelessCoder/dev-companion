# Catalog the backend module chrome as a component entry

**Serves:** feedback/2026-08-10-182511-component-lookup-was-passed-over-while.md
**Priority:** normal
**Branch:** todo/catalog-the-backend-module-chrome-as-a-component
**Claimed:** 2026-08-11

Add a `module` entry to `knowledge/catalog/components.json` for the chrome
around every backend module, read off
`Build/Sources/Sass/component/_module.scss` in `.checkouts/12.4`, `13.4`, `14.3`
and `main`, and bound whole from what those four show — `D-CAT-001`. On `main`
that partial carries the root class `module`, the sub-components
`module-docheader`, `module-docheader-navigation`, `module-docheader-buttons`
and `module-body`, the modifiers `module-layout-wide` and
`module-layout-normal`, and some twenty `--module-*` custom properties; the
other three checkouts decide the `since` and `until`. Its markup comes from
`typo3/sysext/backend/Resources/Private/Layouts/Module.fluid.html` with
`Partials/DocHeader.fluid.html` rather than from a styleguide demo, so
`demoPath` is null and `bin/cli catalog:paths` is what holds the paths.
`D-CAT-004` says why chrome belongs in the index at all, and its first **Wrong
if** is what the markup has to be written against. The feedback stays open while
another todo still serves it — `bin/cli feedback:list` says which.
