---
date: 2026-08-17T21:20:10+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_hint_lookup
directory: /home/benji/projects/site-demo
---

# the knowledge is indexed by subject, and a caller who is debugging has a symptom rather than a su...

## Observation

Task: build a TYPO3 v14 demo site as a sitepackage plus a distribution extension carrying the content, on TYPO3 14.3.6. This is the wish I would put above the others, and it is about the axis the index is sorted on rather than about any single hint.

Every hint is titled after its subject: "Registering a Content Element", "Seeding Records with a Script", "What a Fluid Layout Renders, and What Is Never Executed". That works while planning, and in this session it worked well — I fetched twenty-one hints by id, all of them chosen from a subject I already knew I was about to work on.

It stops working the moment something breaks, because then I do not have a subject. I have an observation:

- "the child rows exist, uid_foreign is 0, tablename is empty, errorLog is empty"
- "the stylesheet is published under _assets but no link tag appears in the page"
- "the content elements render in reverse order"
- "the argument record for partial ... is required, but was not provided"

Each of those had an answer in the index — datahandler-relations, fluid-layouts-sections, datahandler-placement, and for the last one the fluid_styled_content partial collision (which is not covered, filed separately). In three of the four cases the id was listed in an availableHints array sitting in my context at that moment. I read core source instead, and I did that because the translation from symptom to subject is work the caller has to do first, and when you are holding a stack trace the concrete thing in front of you wins over a general index you would have to guess a heading for.

That is nine debugging cycles and roughly 45 round trips in this session — the single largest cost item, larger than every server call put together. Three of the nine would have collapsed to one lookup.

typo3_hint_lookup already takes a free-text `task`, so the surface exists. What I do not know, and did not test at the time, is whether the matching would find fluid-layouts-sections from "f:asset.css does not appear in the output" — the description says matching is lexical, and a symptom shares almost no vocabulary with a title written from the mechanism's side. My assumption while debugging was that it would not, which is why I never tried it. That assumption is worth checking rather than trusting: it may be wrong, and if it is, the fix is documentation rather than data.

## Query

typo3_hint_lookup with task="f:asset.css does not appear in the rendered page" — compare what comes back against the id that would have answered (fluid-layouts-sections)

## Suggestion

Many hints already carry the symptom in their prose — content-element-preview says a template reading {header} "renders an empty spot and logs nothing", sitepackage-templates says the layout collision comes back "on a page that still comes back without an error". Those sentences are exactly what a debugging caller would search with, and fluid-layouts-sections has none for the asset case. Give that hint the words a caller arrives with.

## What is left of this

Trimmed on 2026-08-18. The axis this asked for exists and is now reached across
the domain gate as well — `D-ANS-081` measured it, `D-ANS-084` is the rule, and
"the content elements render in reverse order" returns datahandler-placement.
The `task` parameter and the `routing` block say a symptom is a query this tool
takes, which is the documentation half of the suggestion.

What is left is the one query above. It is not the gate: Fluid is selected, the
hint is inside it, and the query carries none of its words, so this is curation
of that hint.
