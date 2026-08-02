---
date: 2026-08-01T00:29:30+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3_documentation_lookup
directory: /home/benji/projects/site-new
---

# Debrief of a TYPO3 14 backend preview task. The CustomBackendPreview documentation example shows ...

## Observation

Debrief of a TYPO3 14 backend preview task. The CustomBackendPreview documentation example shows <h2>{record.header}</h2> inside the preview template; copying it verbatim produced a duplicate header in the page module, because StandardContentPreviewRenderer already renders the record label (header) into element-preview-header. This cost a user-visible round trip and a vendor-source read before the duplication was understood.

## Query

page=https://docs.typo3.org/m/typo3/reference-coreapi/14.3/en-us/ApiOverview/ContentElements/CustomBackendPreview.html

## Suggestion

Annotate the preview-template example: do not re-render the header/label field the default renderer already outputs; show only the content part (bodytext, relations).
