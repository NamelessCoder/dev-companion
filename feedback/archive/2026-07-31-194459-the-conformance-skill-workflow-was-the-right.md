---
date: 2026-07-31T19:44:59+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/mimo-v2.5-free
directory: /home/benji/projects/site-new
---

# The conformance skill workflow was the right order. Architecture hints were the most valuable par...

## Observation

The conformance skill workflow was the right order. Architecture hints were the most valuable part. Two improvements needed. First the deprecation sweep step produced nothing because the changelog tool could not match the queries. The skill should say to fall back to architecture lookup hints when the changelog returns empty. Second the skill says to call architecture lookup for each subsystem before reading the checkout but does not say what to do when those hints already answer the question the changelog was supposed to answer.

## Query

conformance audit workflow for printworks sitepackage on TYPO3 14

## Suggestion

Add a note to base.md step 5 that when the changelog returns nothing the architecture hints for the same paths are sufficient evidence. Also add a note that architecture lookup and changelog lookup cover overlapping ground for version specific changes.
