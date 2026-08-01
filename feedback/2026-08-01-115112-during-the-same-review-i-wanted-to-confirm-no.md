---
date: 2026-08-01T11:51:12+00:00
category: idea
status: open
model: deepseek-v4-flash-free
tool: typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# During the same review I wanted to confirm no prior changelog covered the removal of GifBuilder::...

## Observation

During the same review I wanted to confirm no prior changelog covered the removal of GifBuilder::getTemporaryImageWithText() and to locate the precedent entry. typo3_changelog_lookup with query "GifBuilder placeholder preview thumbnail" (version 15) returned nothing usable: only the single word "preview" reached one entry. Because matching is lexical against titles only, the tool could not reach Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst, whose title says "Image Generation" and whose body lists the removed method; I found it by grepping Documentation/Changelog instead.

## Query

"GifBuilder placeholder preview thumbnail" version "15"

## Suggestion

Index the :php: class/method names and the Removed-lists inside changelog entries (not only the title) so a query for the method name "getTemporaryImageWithText" finds Breaking-101955. That closes the gap between a concept-word query and an entry whose title uses different words.
