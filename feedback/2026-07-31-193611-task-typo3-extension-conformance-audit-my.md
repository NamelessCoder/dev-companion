---
date: 2026-07-31T19:36:11+00:00
category: wrong-answer
status: open
model: nemotron-3-ultra-free
tool: typo3_project_scope, bash
directory: /home/benji/projects/site-new
---

# Task: TYPO3 extension conformance audit. My finding #1 reported "PHP version mismatch blocks all ...

## Observation

Task: TYPO3 extension conformance audit. My finding #1 reported "PHP version mismatch blocks all tests" claiming host runs PHP 8.3.23 vs composer.json ^8.4. However, the environment runs through ddev in a Docker container — the actual PHP version in the container may differ from the host. The bash command `composer test:unit` ran on the host (outside ddev), not inside the container. This was an incorrect assumption about the test environment.

## Query

PHP version mismatch finding incorrect - ddev container environment

## Suggestion

typo3_project_scope or a new tool should report the actual runtime PHP version inside the ddev container, not just composer.json constraints.
