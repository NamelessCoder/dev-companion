---
description: >-
  Every route a package's own CSS and JavaScript takes into a rendered page, backend and frontend, and how each one is checked after a rebuild.
whenToUse: >-
  After a build wrote different files than it did before — renamed, split, hashed or moved — and before changing where a build writes. It names the route each output file takes and what proves the route still carries; a broken route raises nothing in PHP and shows as a page without its styles.
hints:
  - public-assets
  - extension-asset-build
  - extension-declarative-files
---

# How a Package's Asset Reaches a Page

A file below `Resources/Public/` is not loaded because it is there. Every route
is a declaration somewhere else, and a build that renames or moves an output
breaks the declaration without anything failing in PHP. What follows is the
routes, and what proves each one still carries.

Two things run underneath all of them and are not repeated here: the file has to
be published into the document root, which `public-assets` covers, and it has to
be addressable, which the same hint covers for a build directory outside the
package's default paths.

## The Backend Import Map, for JavaScript

`Configuration/JavaScriptModules.php` maps a bare specifier onto a path in the
package and lists the extensions whose modules it depends on. It is the only way
built backend JavaScript reaches the backend, and `extension-declarative-files`
describes the file itself.

**Backend JavaScript is not bundled.** It is delivered as ES modules, one
specifier per file, so a pipeline that emits one hashed bundle produces
something the map cannot name.

**Checked statically.** The map is a file and the build wrote files, so every
mapped path is compared against what is on disk. That comparison is the whole
check and it needs nothing running.

## The Module Template, for a Backend Module

A backend module does not declare its assets in a configuration file. It calls
`PageRenderer` — `loadJavaScriptModule()` for a specifier the import map
resolves, `addCssFile()` for a stylesheet — and the module template does the
same for what the backend itself needs.

**Checked by reading the call site.** The path or specifier is written in PHP,
so it is found by reading the controller; whether the call runs is a question
about the request rather than about the build.

## TypoScript, for the Frontend

`page.includeCSS.<key>` and the `includeJS` family name a file per key, and the
key is what another setup can override or unset. This is the commonest route a
package's own stylesheet takes.

**Not checked statically.** Whether the file reaches the page is decided by the
TypoScript that resolves for one site, so what proves it is the resolved setup
or a rendered page — never the presence of the file.

## The Asset Collector, from a Template

`<f:asset.css>` and `<f:asset.script>` register a file with the `AssetCollector`
rather than rendering anything where they stand. The collector deduplicates by
the identifier given, which is what lets a partial rendered many times
contribute one tag.

**Where the call sits decides whether it runs at all.** A call outside the
section being rendered registers nothing, leaves the file published and unasked
for, and produces no request and therefore no 404 to find —
`fluid-layouts-sections` carries that trap.

## The Asset Collector's Later Arrivals

**Since:** 13

`<f:asset.module>` registers an ES module by its bare specifier, so a template
reaches the same import map the backend uses.

## Styling One Element From a Template

**Since:** 14

`<f:asset.styleAttr>` collects declarations for a single element rather than a
file, which is a different thing from the routes above: nothing is loaded and
nothing is published.

## From PHP, Anywhere

`PageRenderer::addCssFile()`, `addJsFooterFile()` and `addCssInlineBlock()` add
a file or a block directly, and `AssetCollector::addStyleSheet()` and
`addJavaScript()` do the same through the collector. Both are available in the
frontend and the backend.

**Checked by reading the call site**, as with a backend module.

## What to Do After a Rebuild

The order is the same whichever route a file takes:

1. List what the build wrote, by name, and compare it with what it wrote before.
2. For every renamed, moved, split or dropped file, find the declaration that
   names it. The import map is a file; a TypoScript key, a ViewHelper argument
   and a PHP call are found by searching the package for the old name.
3. Where a file moved out of the package's default public paths, the publish
   step is the finding rather than the build, and `public-assets` says what
   makes it addressable again.
4. What no declaration names is either dead output or a route nobody has found
   yet, and saying which of the two it is takes reading rather than guessing.
