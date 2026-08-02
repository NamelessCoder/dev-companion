---
date: 2026-07-31T19:48:23+00:00
category: idea
status: closed
closed: 2026-08-02
model: opencode/deepseek-v4-flash-free
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# Conformance review of a TYPO3 14 site package: asked typo3_architecture_lookup about the impexp s...

## Observation

Conformance review of a TYPO3 14 site package: asked typo3_architecture_lookup about the impexp site-configuration import (ImportSiteConfigurationsOnPackageInitialization). It stated that only rootPageId is remapped on import and that other uid references are not, which I confirmed against the vendor source. That turned a would-be wrong finding ("the shipped site-config import handles uids, so errorContentSource is fine") into the correct, prioritized finding about the hardcoded t3://page?uid=2 404 target. The negative space — what the mechanism does NOT remap — was the useful part, not the happy path.

## Query

typo3_architecture_lookup task about the impexp site configuration import / ImportSiteConfigurationsOnPackageInitialization

## Suggestion

Keep answering what a mechanism does not cover as precisely as what it covers. That asymmetry is what made the answer decisive here; losing it would have cost me a wrong finding.
