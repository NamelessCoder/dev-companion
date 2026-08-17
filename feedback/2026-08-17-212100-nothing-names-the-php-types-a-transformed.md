---
date: 2026-08-17T21:21:00+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# nothing names the PHP types a transformed record and its link columns arrive as, which f:argument...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. Writing the Fluid partials for six custom content elements.

Fluid 5 in v14 lets a partial declare its arguments with f:argument, and typed arguments are validated at render time. theme_camino's own partials use it — Partials/Atoms/Image.fluid.html declares type="TYPO3\CMS\Core\Resource\FileReference" — so the pattern is visible in the reference implementation the sitepackage-layout hint sends you to read, and I copied the practice.

What no hint states is what the two most common values actually are as PHP types. The record-transformation processor is named in several hints and its output is written as {record.field} throughout, which reads as an array. It is not. I declared type="array" for record and type="string" for a link column and both failed at render:

- record is TYPO3\CMS\Core\Domain\RecordInterface
- a TCA type=link column arrives as TYPO3\CMS\Core\LinkHandling\TypolinkParameter

The first I could infer from the exception's mention of RecordInterface. The second I got only by parsing the class name out of the rendered exception page — "registered with type \"string\", but the provided value is of type \"TYPO3\\CMS\\Core\\LinkHandling\\TypolinkParameter\"" — after two grep passes through cms-core and cms-frontend that found the FieldTypeFactory mapping but not the value object. Five round trips for two class names.

The gap is narrow and the surface is wide: every sitepackage that writes typed partials for content elements hits it, and it hits at render time rather than at build time. The hints that describe this territory — sitepackage-templates, content-element-preview, preview-record-variable, frontend-records, record-system-properties — all discuss what record carries and how to address it, and none says what it is. content-element-preview does say, correctly and usefully, that the preview template is handed one variable named record and that reading {header} renders an empty spot; the natural next question for anyone writing f:argument is what to declare it as, and the answer stops one sentence short.

A related consequence I could not resolve confidently: I do not know whether a link column is a TypolinkParameter in the backend preview context as well as the frontend one, so I declared the same type in both and have no evidence for the preview side beyond it not having failed.

## Query

typo3_hint_lookup id=sitepackage-templates / id=content-element-preview / id=frontend-records targetVersion=14, then write a Fluid partial declaring &lt;f:argument name="record" type="array"/&gt; and &lt;f:argument name="link" type="string"/&gt; and render a content element through lib.contentElement on TYPO3 14.3.6

## Suggestion

Name the types where record is already described. One line in content-element-preview or preview-record-variable — record is TYPO3\CMS\Core\Domain\RecordInterface, not an array, so a typed f:argument must declare the interface — and one beside the record-transformation description for the columns whose values are objects rather than scalars, with type=link arriving as TYPO3\CMS\Core\LinkHandling\TypolinkParameter. Worth stating that the failure is at render time, since a wrong f:argument type is not caught by anything earlier. If other TCA types also transform into value objects (file relations into FileReference is visible in camino, and datetime is a plausible third), listing that set once would be more useful than any single entry.
