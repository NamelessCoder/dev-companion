---
date: 2026-07-29T16:11:32+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: bd02e91
subject: "[FEATURE] Say of an icon identifier that it is the backend's"
tool: typo3_icon_lookup
directory: /home/benji/projects/site-new
---

# The answer is correct but says nothing about where the identifiers may be used, and I was about t...

## Observation

The answer is correct but says nothing about where the identifiers may be used, and I was about to use one in a frontend template. The user stopped me: "die icons welche du findest sind übrigens nur für das backend gedacht, nicht für das frontend".

The registry the tool reads is the backend icon registry. An identifier out of it is resolved by IconFactory and rendered by <core:icon>, which is a backend ViewHelper; a frontend template has no access to any of it and needs its own inline SVG or asset. Nothing in the tool's own description, in its answers, or in the routing entry ("When: About to reference an icon identifier") mentions that boundary — and the routing entry in particular reads as if it applied to any icon in any context.

The same holds for typo3_component_lookup, which does state its backend scope, and for typo3_architecture_lookup, which withholds the Backend CSS and Backend TypeScript hints when a task names the frontend. The icon lookup has no equivalent guard: ask it while working on a frontend template and it answers as if the identifiers were usable there.

## Query

typo3_icon_lookup query="product package box" → actions-package, content-package, form-checkbox, form-multi-checkbox

## Suggestion

Carry a scope statement in every typo3_icon_lookup answer — one line saying these identifiers address the backend icon registry and are not available to frontend rendering. Better still, mirror what typo3_architecture_lookup already does: when the query context reads as frontend, say so instead of just answering. And narrow the routing entry from "About to reference an icon identifier" to "About to reference an icon identifier in backend context (TCA, modules, content element wizard)".
