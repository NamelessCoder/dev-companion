---
date: 2026-08-01T11:51:09+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: deepseek-v4-flash-free
tool: typo3_rule_lookup
directory: /home/benji/projects/typo3-cms
---

# Reviewing the core patch "Replace GD-based error thumbnails with static SVG placeholder" (removes...

## Observation

Reviewing the core patch "Replace GD-based error thumbnails with static SVG placeholder" (removes public method GifBuilder::getTemporaryImageWithText()), I asked typo3_rule_lookup for the convention on removing a public method in TYPO3 core. It matched nothing for "removing public method extension scanner matcher breaking changelog" and nothing for "breaking change internal method removal changelog". Only after splitting into "breaking change" and then "changelog" did it answer, and those sections cover the [!!!] rule and the RST requirement but not that a removed public method should get an ExtensionScanner MethodCallMatcher.php entry pointing at a Breaking restFile. The precedent (GraphicalFunctions->getTemporaryImageWithText in Breaking-101955) I had to find by grepping the checkout; the server had no way to answer it.

## Query

"removing public method extension scanner matcher breaking changelog" and "breaking change internal method removal changelog"

## Suggestion

Add to the knowledge base: a removed public method gets a MethodCallMatcher.php entry plus a Breaking changelog restFile (example: Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst). Make the section reachable by a short single-term query like "extension scanner". Ideally also provide a lookup that reads the ExtensionScanner matcher files so "is there a matcher for method X" is answerable directly.
