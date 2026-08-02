---
date: 2026-08-02T15:06:25+00:00
category: bug
status: open
model: claude-opus-5[1m]
tool: typo3_feedback_record, typo3_feedback_list
directory: /home/benji/projects/typo3-cms
---

# Task: file feedback about TYPO3 core work, naming the skills that did not activate.

## Observation

Task: file feedback about TYPO3 core work, naming the skills that did not activate.

I passed the seven skill identifiers exactly as the skill listing spells them, hyphenated. What was persisted and what typo3_feedback_list returns has the hyphens stripped: "typo3backendmoduledevelopment", "typo3extensionconformance", and so on. The tools[] array carries the same stripped form.

Retrieval is not broken — filtering with tool="typo3-extension-conformance" does find the entry, so the query is normalised the same way. The problem is downstream of that. A maintainer reading the list sees identifiers that do not exist anywhere in the project, and anyone grepping the feedback markdown files for the documented skill name "typo3-extension-conformance" finds nothing, which is the most natural way to answer "what has been reported about this skill". The tool's own description invites naming a skill here ("or the skill it activated, for example typo3-extension-conformance"), spelled with hyphens, so the stored form contradicts the documented input.

The same normalisation presumably applies to any tool name containing a hyphen.

## Query

typo3_feedback_record with tool=["typo3-backend-module-development","typo3-content-element-development","typo3-extension-conformance","typo3-extension-documentation","typo3-extension-release","typo3-extension-testing","typo3-extension-upgrade"], then typo3_feedback_list to verify

## Suggestion

Keep the identifier as given when persisting and when listing, and normalise only for matching — the filter already normalises both sides, so preserving the original spelling costs nothing and makes the stored feedback greppable by the name the skill actually has.
