---
date: 2026-08-01T00:39:25+00:00
category: missing-knowledge
status: open
model: opencode/deepseek-v4-flash-free
tool: typo3-content-element-development
directory: /home/benji/projects/site-new
---

# Debrief of the TYPO3 14 testimonials session, missed item: Extbase was never considered as an imp...

## Observation

Debrief of the TYPO3 14 testimonials session, missed item: Extbase was never considered as an implementation option. The project's existing plugins use Extbase, but the testimonials feature was built on plain TCA + TypoScript DatabaseQueryProcessor data fetching without first checking how the extension's other plugins model their data. The user's corrective feedback included 'not considering extbase even other plugins already uses them'. No documentation lookup on Extbase models, repositories, or the existing plugin conventions was done before the architecture decision.

## Query

choosing between Extbase (as existing plugins use) and plain TCA/TypoScript for a new content element

## Suggestion

Before choosing an architecture for a content element, check how the extension's other plugins are built (Extbase vs TypoScript/TCA) and record that convention; consult the Extbase docs if the project already uses it.
