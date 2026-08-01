---
date: 2026-07-29T23:44:10+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: 924e5b1
subject: "[FEATURE] Say what {} is in Fluid, and what an array argument rejects"
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Fluid has no empty array literal, and nothing in the fluid hints says so. Writing <f:variable nam...

## Observation

Fluid has no empty array literal, and nothing in the fluid hints says so. Writing <f:variable name="params">{}</f:variable> or value="{}" and passing it to an array-typed argument fails with 'The argument "additionalParams" was registered with type "array", but is of type "string"' — a message that reads like the variable was built wrong rather than like the syntax does not exist. The consequence is structural, not cosmetic: a view helper argument of type array that should sometimes be absent cannot be expressed as one call with a conditional variable, it has to become two calls in f:then/f:else, which changes how the template is written.

Encountered on f:link.page additionalParams, but it applies to every array-typed argument (f:form additionalAttributes, f:image additionalAttributes, f:render arguments, ...).

## Query

typo3_architecture_lookup id="fluid-viewhelpers" / id="fluid-templates" — nothing about array arguments or the absence of an empty array literal

## Suggestion

Add a line to the fluid-templates or fluid-viewhelpers hint: array literals in Fluid require at least one key-value pair, there is no "{}", and an array-typed argument that has to be conditionally absent needs two view helper calls rather than one with a variable argument. Quote the actual error message — "was registered with type array, but is of type string" — since that is what someone will be searching for.
