---
date: 2026-08-21T07:43:51+00:00
category: idea
status: closed
closed: 2026-08-21
model: claude-opus-5[1m]
tool: typo3_fluid_namespace_list
---

# the ViewHelpers an installation has are not answerable, only the namespaces they live in

## Observation

typo3_fluid_namespace_list answers which Fluid namespaces are globally registered, so a template knows which prefixes it may use undeclared. What it does not say is where the ViewHelpers behind a prefix are described: what arguments one takes, which of them are required, and what it is for. A ViewHelper argument name fails only at runtime — wrong, it throws when the template renders.

## Query

Task: compare this server against another TYPO3 MCP server. No failing call — typo3_fluid_namespace_list answers the prefixes and stops there.

## Suggestion

Say it in the hints a template task already reaches. fluid-viewhelpers is written for somebody authoring a ViewHelper, and fluid-templates routes an icon and a backend component but not an argument list. Neither says that a core ViewHelper's arguments come from the Fluid ViewHelper Reference through typo3_documentation_lookup, or that one outside the core is read from the class its identifier resolves to in the installed package.

## What is left of this

Trimmed on 2026-08-21. The tool this asked for is refused and the reading is in
`D-ANS-003`: the argument list of a core ViewHelper comes back from the
ViewHelper Reference in one call, and one outside the core is a class in the
caller's own tree, which the namespace list already resolves the prefix half of.

What is left is the routing above. Nothing in the corpus tells a session writing
a template where either of those two lives.
