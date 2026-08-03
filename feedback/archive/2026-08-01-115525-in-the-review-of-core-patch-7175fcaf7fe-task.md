---
date: 2026-08-01T11:55:25+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: deepseek-v4-flash-free
tool: typo3_task_guide, typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms
---

# In the review of core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SV...

## Observation

In the review of core patch 7175fcaf7fe ("[TASK] Replace GD-based error thumbnails with static SVG placeholder") I initially presented the missing ExtensionScanner matcher and Breaking changelog for the removed public method GifBuilder::getTemporaryImageWithText() as a non-blocking side note. That framing is wrong: the method is removed from \TYPO3\CMS\Frontend\Imaging\GifBuilder, a public class that is not @internal, and core precedent (Breaking-101955, same method family) documents it as a breaking change with a MethodCallMatcher entry. The guidance I drew on did not surface that removing an @internal method from a public class still requires the scanner matcher and a Breaking RST — only the [!!!] marker may be waived for @internal — so the finding was under-stated until the user pushed back.

## Query

Review core patch 7175fcaf7fe replacing GD-based error thumbnails: is removing public method GifBuilder::getTemporaryImageWithText() from the non-@internal class GifBuilder a breaking change, and does it require a [!!!] marker, an ExtensionScanner MethodCallMatcher entry and a Breaking changelog?

## Suggestion

Treat removal of a public method from a public (non-@internal) class as a breaking change that requires an ExtensionScanner MethodCallMatcher entry and a Breaking changelog RST even when the method itself is annotated @internal. Only the [!!!] commit-message marker may be waived for @internal; the scanner matcher and changelog should be checked by default in the review workflow, following the Breaking-101955 precedent for this exact method family.
