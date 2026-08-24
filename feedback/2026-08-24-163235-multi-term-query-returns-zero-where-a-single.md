---
date: 2026-08-24T16:32:35+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# Multi-term query returns zero where a single term returns hits, with nothing saying terms are ANDed

## Observation

Task: find Forge issues in the "asset renderer" area of the TYPO3 core. "Asset renderer" is ambiguous in this codebase — it can mean AssetCollector/AssetRenderer (frontend assets, f:asset.* ViewHelpers) or the FAL RendererRegistry/FileRendererInterface (media renderers) — so I searched both readings.

Two of my calls returned nothing usable, and both for the same reason:

1. query="file renderer RendererRegistry FileRendererInterface" -> total 0, status "empty". I then called query="RendererRegistry" alone -> total 5, all real hits. So the multi-term query matched nothing that the single term matched. That is one wasted round trip and, worse, a false negative: had I stopped at the first call I would have concluded the FAL renderer area has no issues at all, which is not what the tracker says.

2. query="assets inline javascript stylesheet rendering frontend" -> total 1, and that one hit was an unrelated 2017 open_basedir warning. Six plausible terms produced one irrelevant result.

Meanwhile query="AssetCollector" -> 52 and query="AssetRenderer" -> 15, both useful.

The parameter description for `query` says it is "A full-text search over subject, description and comments" and warns that "one wording does not settle it: ask again in the reporter's words as well as your own". It does not say that terms are combined with AND, which is what the results imply. That distinction changes how you write the query: under AND you use one or two terms and vary them across calls; under OR you can throw in every synonym at once. I wrote as if it were OR and got zeroes.

Version context: TYPO3 main, BRANCH 15.0, checked from Typo3Version in the checkout.

## Query

typo3_forge_lookup query="file renderer RendererRegistry FileRendererInterface" limit=25 -> total 0; then typo3_forge_lookup query="RendererRegistry" limit=25 -> total 5. Also typo3_forge_lookup query="assets inline javascript stylesheet rendering frontend" limit=25 -> total 1, irrelevant.

## Suggestion

The first half was taken on 2026-08-24 and this is trimmed to the rest. Both the `query` description and the empty answer now say that every word has to be in the same issue, and name a term nobody would have written as what empties an answer — `D-ANS-038`, which also carries the measurement.

What is left is making an empty result self-correcting: when a multi-term query returns 0, retry internally with the individual terms and report "0 for all terms together; RendererRegistry alone matches 5, FileRendererInterface alone matches 0". The server already knows it returned nothing and already has the terms; that turns a false negative into an answer without a second round trip. The existing `categories` field does exactly this kind of correction for a bad category word ("the categories that exist come back with every answer, so a word matching none is corrected without a second call") — the same courtesy for query terms would have saved me a call and prevented a wrong conclusion.
