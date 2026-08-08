---
date: 2026-08-08T22:43:16+00:00
category: missing-knowledge
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_rule_lookup, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# nothing warns that widening a public non-final core method signature breaks subclasses

## Observation

Task: pick an old open core bug, establish whether it still happens on main, and fix it — I landed on #58705, "file:current:* not resolvable inside GIFBUILDER objects", and wrote the patch.

My first draft added an optional third parameter to `GifBuilder::start(array $conf, array $data, ?ContentObjectRenderer $parentContentObject = null)`. `GifBuilder` is not final and `start()` is public and not final, so any extension that overrides `start()` with the two-parameter signature becomes signature-incompatible and fatals on load. That is a breaking change, it would have blocked the backport to 14.3 and 13.4 that the fix needs, and it would have shipped as a plain BUGFIX with no changelog entry.

Nothing in the workflow flagged it. I passed both changed paths to typo3_hint_lookup with a task text that says outright I am changing how GIFBUILDER receives state from the calling ContentObjectRenderer; it returned fal-processing, fal-basics and system-extension-boundaries — all about FAL and none about the public API surface of the class I was editing. The full run of the project's own checks did not catch it either: functional (3613 tests), unit (235), cgl (6300 files), checkIntegrityPhp and phpstan (6265 files) were all green on the breaking draft, because no core class overrides `GifBuilder::start()`.

What finally raised it was typo3_commit_message_guide's `breaking-not-assessed` info line — "a removed or narrowed public or protected member makes the change breaking" — which arrived after the diff was finished and after every suite had been run. Prompted by it I reconsidered, replaced the signature change with a purely additive `setParentContentObject()` setter, and re-ran everything. The prompt worked, but it fired at commit-message time, which is the last place in the order where a diff can still be reshaped cheaply, and its wording covers removing and narrowing rather than widening.

## Query

typo3_hint_lookup(paths=["typo3/sysext/frontend/Classes/Imaging/GifBuilder.php","typo3/sysext/frontend/Classes/ContentObject/ContentObjectRenderer.php"], task="Propagate the current FAL file from the calling ContentObjectRenderer into GIFBUILDER child content objects", targetVersion="15")

## Suggestion

Carry the rule where a session writing PHP will meet it before the diff is finished, not at commit-message time: a hint on core `Classes/**/*.php` paths, along the lines of "a public or protected method on a non-final class is an override point — adding a parameter to it, even an optional one, makes every subclass that overrides it signature-incompatible. Hand new state over with an additive setter, or make the class or method final first." Widening should be named beside removing and narrowing in typo3_commit_message_guide's `breaking-not-assessed` line and in whatever typo3_rule_lookup(query "breaking change") returns, because "narrowed" reads as not covering an added optional parameter. Backportability is the other half worth saying: a change that has to reach 14.3 and 13.4 cannot carry a signature change at all.
