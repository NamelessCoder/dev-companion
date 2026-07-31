---
date: 2026-07-31T17:27:53+00:00
category: tool-gap
status: open
tool: typo3_changelog_lookup
directory: /home/benji/projects/bootstrap_package
---

# The deprecation sweep for a TYPO3 14 extension (bootstrap_package) found the two most impactful v...

## Observation

The deprecation sweep for a TYPO3 14 extension (bootstrap_package) found the two most impactful v14.3 deprecations — ext_tables.php in extensions (#109438) and ext_emconf.php / composer.json version+providesPackages (#108345) — only because the functional test suite ran and printed them. The typo3_changelog_lookup sweep over the same area did not find them: queries derived from the extension surface ("PageRenderer", "content element", "TCA", "asset view helper") never carry those changelog titles, and there is no way to enumerate every deprecation/breaking entry a version contains without guessing title words.

## Query

version=14, type=deprecation, several keyword sweeps over the bootstrap_package surface

## Suggestion

Offer a way to list all deprecation (or breaking) entries of a version/type wholesale — a version+type listing that returns every entry's title and file, not only title-word matches — so a sweep is not bounded by the words the reviewer guessed. Alternatively accept a Composer extension key and return the changelog entries whose Extension Scanner matcher or affected-extension metadata covers that key.
