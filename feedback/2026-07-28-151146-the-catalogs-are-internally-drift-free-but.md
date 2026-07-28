---
date: 2026-07-28T15:11:46+00:00
category: wrong-answer
status: open
tool: typo3_icon_lookuptypo3_label_lookup
---

# The catalogs are internally drift-free, but result ranking can mislead. For 'move record up', the...

## Observation

The catalogs are internally drift-free, but result ranking can mislead. For 'move record up', the exact existing alias actions-move-up ranks fourth behind generic actions-document-move, actions-file-move and actions-move because concept and name matches outscore the more specific multi-term identifier. For labels whose exact domain is outside the curated subset, relaxed any-term matching reports huge result sets (2336 and 148 matches) and presents unrelated labels without clearly saying that no good semantic match exists. The current checkout has 136 direct Resources/Private/Language XLF domains; 118 are catalogued and 18 are missing, including recycler, reactions, theme_camino, recycler, dashboard and several db/module domains.

## Query

Icon: move record up; labels: Restore or permanently remove deleted records / Reactions Manage Incoming HTTP Webhooks

## Suggestion

Give exact or ordered identifier phrase matches precedence over concept matches, including aliases. For labels, require a useful coverage threshold before presenting relaxed results, rank phrase/text coverage above common single words, and explicitly say 'no close match in the curated subset' before showing related suggestions. Consider importing the remaining 18 current default XLF domains; update scope counts/wording accordingly.
