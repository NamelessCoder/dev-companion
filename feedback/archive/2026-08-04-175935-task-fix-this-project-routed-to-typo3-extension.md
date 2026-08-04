---
date: 2026-08-04T17:59:35+00:00
category: idea
status: closed
closed: 2026-08-04
model: claude-opus-5
tool: typo3-extension-conformance
directory: /home/benji/projects/site-new
---

# Task: "fix this project" — routed to typo3-extension-conformance for the audit, then typo3-extens...

## Observation

Task: "fix this project" — routed to typo3-extension-conformance for the audit, then typo3-extension-cleanup to work the findings off.

I marked the Quality surface assessed and reported the documentation entry as clean-by-absence: typo3_extension_describe returned manual: null and readme: null, and I applied the skill's own rule that "absence of an optional subsystem is not a defect" to conclude a project sitepackage needs no manual.

I never called typo3_hint_lookup id=extension-documentation. The skill's method says a surface is not assessed until both the runtime lookup and the conventions lookup were asked; I had the runtime fact and skipped the convention. The convention is not silent — it says an extension's manual lives in Documentation/ with Index.rst and guides.xml — so the verdict I published was formed against exactly the rule the method exists to prevent.

The user caught it afterwards ("you did not create the typo3 recommended documentaiton for extensions?"). It was a real gap and I built the manual in the follow-up.

Why it slipped, concretely: the surfaces that did get their convention asked are the ones whose hint id is written down where I was working. static-quality.md names extension-static-analysis and extension-coding-standards inline, and I called both. The Quality row of checklist.md names documentation as a bare word, and I did not.

## Query

Full conformance audit of a project sitepackage; the Quality surface of references/checklist.md, which names "documentation" among "the test suite ..., the check layer, documentation, deprecations, and upgrade readiness"

## Suggestion

Name the hint id in the checklist row, the way static-quality.md already does for the analyser and the fixer: "documentation (extension-documentation)". A surface whose id is written down gets asked; one described only as a noun gets reasoned about instead. The same probably applies to the other bare nouns in that row — "deprecations" got asked only because the base fixes a whole deprecation-sweep step around it.
