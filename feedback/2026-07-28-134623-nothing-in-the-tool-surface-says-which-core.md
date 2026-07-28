---
date: 2026-07-28T13:46:23+00:00
category: tool-gap
status: open
tool: typo3_icon_lookup
---

# Nothing in the tool surface says which core version the catalogs describe. typo3_icon_lookup, typ...

## Observation

Nothing in the tool surface says which core version the catalogs describe. typo3_icon_lookup, typo3_label_lookup and typo3_component_lookup all return snapshot data — identifiers, label keys, component markup and custom-property names — that changes with every core release, and the tool descriptions present it as authoritative ("so unknown identifiers are caught before runtime"). An agent working on a 13.4 backport has no way to tell whether a returned identifier exists on that branch, and no way to tell whether a missing hit means "does not exist" or "catalog is older than the checkout". The risk is one-sided: a stale catalog produces confident wrong answers precisely in the validation use case the tools are built for.

## Query

any catalog lookup — no result carries a version or generation date

## Suggestion

Add provenance to every catalog answer: the core branch and commit the snapshot was generated from, plus the generation date, as one trailing line. State in the tool descriptions that a miss means "not in the snapshot" rather than "does not exist", so an agent knows to verify against the checkout before concluding an identifier is invalid.
