---
date: 2026-07-31T17:27:53+00:00
category: tool-gap
status: closed
closed: 2026-08-02
tool: typo3_changelog_lookup
directory: /home/benji/projects/bootstrap_package
---

# The deprecation sweep for a TYPO3 14 extension (bootstrap_package) found the two most impactful v...

## Observation

Trimmed on 2026-08-02 to the part that is left. Two of the three things this
reported are answered: `ext_tables.php` (#109438) and `ext_emconf.php` (#108345)
reach their entries now — an identifier is compared without its separators, so
the caller's spelling of the thing finds the entry the file names in words — and
a version or a type can already be listed whole by omitting the query, which the
tool says on the `query` property and the sweep did not use.

What is left is the third: a sweep bounded by the words the reviewer guessed.
Deriving queries from the extension surface ("PageRenderer", "content element",
"TCA") never carries a changelog title, and enumerating a version whole hands
back 75 deprecations for TYPO3 14 with nothing saying which of them touch this
extension.

## Query

version=14, type=deprecation, several keyword sweeps over the bootstrap_package surface

## Suggestion

Accept a Composer package or extension key and return the entries whose index
tags name it — the entries carry `ext:core`, `ext:fluid` and the Extension
Scanner state (`FullyScanned`, `NotScanned`) already, and `Changelog::read()`
parses both. That is the filter a deprecation sweep for one extension needs, and
it is a property of the entry rather than of the words in it.
