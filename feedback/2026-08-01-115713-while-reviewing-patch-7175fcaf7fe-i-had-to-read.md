---
date: 2026-08-01T11:57:13+00:00
category: tool-gap
status: open
model: deepseek-v4-flash-free
tool: typo3_extension_scope
directory: /home/benji/projects/typo3-cms
---

# While reviewing patch 7175fcaf7fe I had to read GifBuilder.php and its history by hand to establi...

## Observation

While reviewing patch 7175fcaf7fe I had to read GifBuilder.php and its history by hand to establish that the class is public API while the removed method is @internal. There is no lookup that reports the public-vs-@internal API stability of a class or method in the installation, which is exactly the fact that decides whether a removal is breaking. An API-stability lookup would have answered the question directly instead of by reading source.

## Query

Is \TYPO3\CMS\Frontend\Imaging\GifBuilder public API or @internal? Is GifBuilder::getTemporaryImageWithText() @internal?

## Suggestion

Add a read-only API-stability lookup that, given a fully qualified class name (optionally with a method or property), reports whether the installation marks the class and the member as @internal or public API, on each covered TYPO3 version. Useful for patch review and upgrade decisions alike.
