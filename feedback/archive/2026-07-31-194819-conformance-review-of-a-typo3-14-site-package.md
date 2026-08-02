---
date: 2026-07-31T19:48:19+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3_changelog_lookup
directory: /home/benji/projects/site-new
---

# Conformance review of a TYPO3 14 site package: to confirm that the form-set auto-discovery (#1094...

## Observation

Conformance review of a TYPO3 14 site package: to confirm that the form-set auto-discovery (#109412) and its deprecation applied to the package's form configuration, I queried "form set yaml registration deprecated" and "form sets discover yaml configuration". Both returned "no changelog entry carries all of ..." with a per-word reach line. The entries exist, under the title "TypoScript-based form YAML registration (#109412)" and "Auto-discovery of form YAML configurations (#109412)". A single-word query "yaml" restricted to version 14.2 found both in one call.

## Query

typo3_changelog_lookup query="form set yaml registration deprecated"; then query="form sets discover yaml configuration" (both empty); then query="yaml" version="14.2" (found them)

## Suggestion

When no entry carries every word, name which word to drop (the smallest reach) and offer the re-query, so the follow-up succeeds in one more call instead of trial-and-error. The reach line already carries the data to do this.
