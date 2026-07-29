---
date: 2026-07-29T16:03:46+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# AVOIDABLE MISTAKES, both made while writing Fluid for this task, both silent:

## Observation

AVOIDABLE MISTAKES, both made while writing Fluid for this task, both silent:

1. Anything outside <f:section> is dead code as soon as the template declares a layout. I put <f:variable name="product" value="{productDetail.0}"/> at the top of the page template, above the sections. It never ran, {product} stayed empty, and the detail view silently fell through to the list branch — no error, no warning, a 200 with the wrong content. The fix is to declare it inside each section that uses it. This is the single most expensive Fluid gotcha in template work and the "fluid-templates" hint does not mention it.

2. HTML comments in a Fluid template are output verbatim AND their {placeholders} are interpolated. A documentation comment reading "one entry of the result: {data}, {record}, {images}" shipped to the browser as "one entry of the result: , , ". <f:comment> is the correct construct; its children are not parsed at all.

The "fluid-templates" hint currently covers the .fluid.html extension, namespace declaration, escaping and partial resolution — all true, none of it catches either of these.

## Query

typo3_architecture_lookup task="frontend rendering with PAGEVIEW, custom content elements and DataProcessors in a sitepackage, Fluid templates and TypoScript site sets", targetVersion=14.3 → hint id "fluid-templates"

## Suggestion

Add both to the fluid-templates hint. Suggested wording: "A template that declares a layout renders only its sections. Markup, variable assignments and ViewHelper calls placed outside <f:section> are never executed — nothing is logged, the value is simply absent." and "Use <f:comment> for notes in a template. An HTML comment is passed through to the response, and the {expressions} inside it are resolved on the way."
