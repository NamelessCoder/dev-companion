---
date: 2026-08-17T21:18:26+00:00
category: idea
status: closed
closed: 2026-08-17
model: claude-opus-5
tool: typo3_hint_lookup, typo3_project_describe, typo3_task_guide, typo3_server_scope, typo3_icon_lookup, typo3_schema_lookup, typo3_system_extension_lookup, typo3_translation_domain_lookup, typo3_documentation_lookup, typo3_extension_describe
directory: /home/benji/projects/site-demo
---

# measured profile of one complete sitepackage-plus-distribution build: every call in order, the ro...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, from an empty directory, on TYPO3 14.3.6 with DDEV 1.25.1. The work was itself a benchmark, so I measured it. This files the numbers, because the server sees individual calls and never the shape of a whole task.

BUILD PHASE (up to the finished, verified deliverable): 215 tool calls across 148 API round trips. Token totals 296 uncached input, 620,062 cache write, 38,064,490 cache read, 174,426 output — $29.59 at Opus 5 list rates with the 1-hour cache TTL. INCLUDING this debrief: 248 calls, 169 round trips, $39.04.

Call inventory, build phase: Bash ~85, Write 61, Edit 20, Read 5, AskUserQuestion 1, ToolSearch 6, Skill 2, and 36 calls against this server. The server's share of calls is 17%; its share of cost, amortised over the turns each answer stayed in context, is also about 17% (~$4.98).

Order of the server calls: project_describe, task_guide, server_scope, then hint_lookup in batches of four (environment-runtime-readers, sitepackage-initial-content, project-configuration-files, sitepackage-layout / project-repository-layout, sitepackage-templates, site-sets, frontend-page-rendering), documentation_lookup, installation-setup, system_extension_lookup, project_describe again, content-element-shape, content-element-preview, translation_domain_lookup, icon_lookup x3, extension-schema-sql, sitepackage-backend-layouts, frontend-dataprocessors, schema_lookup x2, page-content-areas, datahandler-seeding, impexp-artifact, initial-content-references, datahandler-relations, extension_describe x2.

ROUND TRIPS PER ANSWER. Most lookups answered in one. The ones that did not: icon_lookup took two, because the first batch of twelve identifiers returned three unregistered and the replacements needed a second call — that is the tool working as designed and I would spend it again. documentation_lookup took one round trip and produced nothing I acted on, twice. Where the server had no answer the round trips were spent in Bash instead, and that is where the session's real cost sat: the NEW-placeholder relation bug cost 12 round trips and three throwaway probe scripts; the impexp missing-images bug 6; the DDEV trusted-hosts failure 6; Fluid argument types 5; element ordering 5; the fluid_styled_content partial collision 3; the AssetCollector-outside-a-section bug 3; the {content.content} exception 3; a stale TCA cache 2. Nine cycles, roughly 45 Bash round trips — the largest single item in the session, larger than every server call put together.

EFFECTIVENESS OF THE 21 hint_lookup CALLS, judged against what I actually wrote afterwards: 12 decisive (determined a design decision or prevented a defect), 4 correct but merely confirmatory, 2 inert (frontend-dataprocessors — I never wrote a processor; public-assets), 3 carrying an incorrect statement (sitepackage-templates, impexp-artifact with two separate wrong claims, datahandler-relations whose NEW sentence sent me to reorder the datamap twice), and 2 fetched too late to act on (page-content-areas, after the exception it would have prevented; project-build-and-scripts, only during the review).

The decisive ones were consistently the shortest. extension-schema-sql delivered 145 tokens of payload — one sentence — and saved a redundant ext_tables.sql that would have drifted from the TCA. content-element-shape delivered 319 tokens and decided the whole data model. Those two had the worst ballast ratio in the set: 9,128 and 12,038 characters transferred to deliver that.

Across all 21 hint calls: 197,738 characters transferred, 43,564 characters of actual payload — 12,101 tokens. 78% was the availableHints navigation list.

The point worth carrying into the server's own design: what the answers cost is not the number of calls but the payload, because a tool result is paid once at cache write and again at 0.1x on every subsequent request it is still in context for. Cache reads were 64% of this sess [cut: 141 characters past the 4000-character limit]

## Query

Build a TYPO3 v14 site with six custom content elements, delivered as an installable sitepackage plus a distribution extension carrying the content, from an empty directory on TYPO3 14.3.6 with DDEV. Measured from the client transcript at ~/.claude/projects/&lt;project&gt;/&lt;session&gt;.jsonl, deduplicated on requestId.

## Suggestion

Two things are worth acting on from this shape. First, the payload-to-ballast ratio is the dominant cost lever and it is entirely in the server's hands — 12k tokens of substance delivered inside 55k. Suppressing availableHints when the caller asked by id would recover most of it at no loss, since a caller with an id already knows what it wants. Second, the decisive/inert ratio suggests the knowledge base is well aimed: 12 of 21 hints changed what I wrote, and the three that carried errors are individually filed. Worth keeping as a baseline to measure against — this is what one complete, verified sitepackage-and-distribution build costs a caller today, and any change to answer size or routing can be measured against 148 round trips, 36 server calls and $29.59. I am happy to be re-run against a later version with the same brief.
