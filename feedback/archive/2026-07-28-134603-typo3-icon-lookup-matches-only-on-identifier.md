---
date: 2026-07-28T13:46:03+00:00
category: tool-gap
status: closed
closed: 2026-07-28
commit: c10d448
subject: "Rank catalog lookups by what actually matched"
tool: typo3_icon_lookup
---

# typo3_icon_lookup matches only on identifier substrings, so it fails exactly where an agent needs...

## Observation

typo3_icon_lookup matches only on identifier substrings, so it fails exactly where an agent needs it: finding an icon by what it means. query="warning" returned a single hit, overlay-warning. The core ships actions-exclamation-triangle, actions-exclamation-triangle-alt, actions-exclamation-circle and actions-exclamation — the icons a developer actually reaches for when rendering a warning — and none of them surfaced, because their identifiers spell the concept "exclamation". query="status warning" was worse: it returned status-user-admin, status-user-backend and status-user-frontend, which match only on the token "status" and have nothing to do with warnings. The tool is reliable for validating an identifier you already know, but not for discovering one, even though the description promises discovery.

## Query

query="warning" (1 hit) and query="status warning" (6 unrelated hits)

## Suggestion

Give each identifier a small set of concept keywords in the catalog and match against those too — warning/caution to exclamation-triangle, error/danger to exclamation-circle, add/new to plus, edit to pencil/open, remove to trash. Score full-token matches above single-token ones so "status warning" does not rank status-user-* first. Alternatively, group results by concept and state plainly when a query matched only on a category prefix.
