---
date: 2026-07-29T17:05:53+00:00
category: wrong-answer
status: closed
closed: 2026-07-29
commit: 36252b4
subject: "[TASK] Say in the icon hint too that the registry is the backend's"
tool: typo3_icon_lookup, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# RE-REPORT: neither typo3_icon_lookup nor the icon-usage hint says where these identifiers may be ...

## Observation

RE-REPORT: neither typo3_icon_lookup nor the icon-usage hint says where these identifiers may be used.

I reported this after the user stopped me mid-task: "die icons welche du findest sind übrigens nur für das backend gedacht, nicht für das frontend". I had asked typo3_icon_lookup for a product icon while writing a frontend template, got actions-package and content-package back, and was about to put one in a Fluid page template.

Checking now: icon-usage has been extended and is good — <core:icon>, IconFactory with the IconSize enum, the two TypeScript entry points, shape-not-intent naming, Configuration/Icons.php. Every one of those is a backend path. But the hint never states the boundary, and a reader who is working on a frontend template will not infer it from a list of backend APIs; they will read "here is how you render an icon". typo3_icon_lookup answers still carry no scope note either.

The asymmetry is what makes this worth repeating: typo3_component_lookup states its backend scope, and typo3_architecture_lookup actively withholds the Backend CSS and Backend TypeScript hints when a task names the frontend. The icon side has neither guard, and it is the one a frontend task reaches for by name.</observation>
<parameter name="suggestion">One line in icon-usage — the icon registry serves the backend; a frontend template has no access to it and ships its own SVG — and a scope field on every typo3_icon_lookup answer saying the same. Better still, mirror what typo3_architecture_lookup already does for Backend CSS: when the query context reads as frontend, say so instead of answering as if the identifiers were usable there.

## Query

typo3_icon_lookup query="product package box" (previous round) and typo3_architecture_lookup id=icon-usage (now) — re-report, still unaddressed
