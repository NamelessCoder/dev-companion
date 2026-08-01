---
date: 2026-07-28T15:11:46+00:00
category: missing-knowledge
status: closed
closed: 2026-07-28
commit: 3538cb2
subject: "Catalogue every default language file, not half of them"
tool: typo3_label_lookup
---

# 18 of the 136 default XLF domains in the checkout are missing from the label catalog

## Observation

Remaining part of the original note; the ranking half is fixed — a concept hit
no longer counts a term the identifier name already carried, and a relaxed
label answer now states that nothing matched closely and is capped by a
coverage threshold instead of reporting thousands of labels.

Still open is the coverage itself. The checkout has 136 direct
Resources/Private/Language XLF domains; 118 are catalogued and 18 are missing,
among them recycler, reactions, theme_camino, dashboard, and several db and
module domains. Queries whose label lives in one of those cannot be answered
from the catalog at all, only routed to the derived domain.

## Query

labels: Restore or permanently remove deleted records / Reactions Manage
Incoming HTTP Webhooks

## Suggestion

Import the remaining 18 default XLF domains into knowledge/catalog/labels.json
and update the depth wording and counts in knowledge/server-scope.json to match.
This belongs with the icon catalog coverage gap recorded separately — both are
about what the catalog build reads.
