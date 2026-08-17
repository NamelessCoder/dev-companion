---
date: 2026-08-17T20:58:30+00:00
category: missing-knowledge
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# the shared-root name collision is warned about for layouts and not for partials, where it breaks ...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. Adding this extension's roots to lib.contentElement.

sitepackage-templates warns clearly about one half of this: "The page frame and the content element frame are both called Default, and fluid_styled_content ships a Default of its own, so a shared Layouts/ root without those subdirectories renders the site header and footer inside every single content element — on a page that still comes back without an error." I acted on it, put my layouts in Layouts/Pages/ and Layouts/Content/, and avoided that bug by construction.

The same hazard exists in the partial root and is not mentioned. I wrote Partials/Header/Header.fluid.html, a perfectly ordinary name for a content element's header, and registered partialRootPaths.20. fluid_styled_content ships Partials/Header/Header.fluid.html too, rendered from its Partials/Header/All.fluid.html with different arguments. Mine won on the higher index, and every core element on the page broke: "The argument \"record\" for partial \"template_Header_fluid_html_...\" is required, but was not provided." Both the frontend and the pages carrying a plain `text` element answered HTTP 500.

The failure is louder than the layout one, so it is cheaper to find — three calls, including a directory diff of the two partial roots. But it is the same mistake, made for the same reason, one directory over, and a reader who follows the hint's layout advice has no reason to expect it. fluid_styled_content's partial root also contains Footer/, Media/, Bullets/ and Table/ — Header/Header is the collision most likely to be hit, because a content element frame is the thing a sitepackage writes first.

For the record, my fix was to namespace this extension's element partials under Partials/Element/ rather than to rename around the individual clash.

## Query

typo3_hint_lookup id=sitepackage-templates targetVersion=14, then add partialRootPaths.20 to lib.contentElement with a Partials/Header/Header.fluid.html of one's own on TYPO3 14.3.6

## Suggestion

Extend the existing collision sentence to cover the partial root alongside the layout root, and name Header/Header explicitly as the one that will be hit: fluid_styled_content ships Partials/Header/Header.fluid.html and renders it from Partials/Header/All.fluid.html with its own arguments, so an extension partial of that name breaks every core element on the page rather than only its own. The general rule worth stating once is that partialRootPaths and layoutRootPaths are shared name spaces resolved by index, so an extension that adds a root should put its own files under a directory nothing else uses.
