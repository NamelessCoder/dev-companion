---
date: 2026-08-01T11:57:11+00:00
category: tool-gap
status: closed
closed: 2026-08-04
model: deepseek-v4-flash-free
tool: typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# Reviewing core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SVG place...

## Observation

Reviewing core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SVG placeholder") for the first time, typo3_task_guide produced a task checklist but nothing in it directed the "does this diff remove public API" check, so the breaking aspect of removing GifBuilder::getTemporaryImageWithText() was initially under-stated until the user pushed back. A review/cleanup checklist must force: enumerate public classes, methods and properties the diff removes or renames; for each removal require an ExtensionScanner MethodCallMatcher entry and a Breaking or Deprecation RST; flag the [!!!] marker when the owning class is public API. Method-level @internal only waives the [!!!] marker, never the matcher and the changelog — Breaking-101955 is the precedent.

## Query

typo3_task_guide task "review the core patch replacing GD-based error thumbnails with a static SVG placeholder", changeType cleanup

## Suggestion

Add a review/cleanup section to the task guide for tasks that remove code: list removed or renamed public classes/methods/properties, require a MethodCallMatcher entry plus a Breaking/Deprecation RST per removal, and a [!!!] marker for removals from public classes, with the rule that method-level @internal waives only [!!!], not the scanner matcher or changelog. End with checkExtensionScannerRst/checkRst.
