---
date: 2026-08-24T16:33:21+00:00
category: idea
status: closed
closed: 2026-08-27
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# The repository's own AGENTS.md routes agents around a tool that answers better

Trimmed on 2026-08-25. The changelog half was answered by the split of
2026-08-24 and is recorded under `D-KNW-111`; the commit-message half is
evidence about a boundary and is recorded under `D-AUD-013`. What is left is
below.

## Observation

Task: search Forge for issues in the asset renderer area, fix one, write its changelog and commit message. Working in the TYPO3 CMS core monorepo, which ships an AGENTS.md read into context at session start.

That AGENTS.md competes with this server on Forge. It says: "Issue tracker: forge.typo3.org (Redmine). Query it through the JSON API with curl -H 'Accept: application/json' and the default curl user agent — browser-like user agents hit the Anubis bot check. Useful endpoints: issues/<id>.json (add ?include=journals for comments), search.json?q=<query>, issues.json?project_id=27". I used typo3_forge_lookup instead and it was clearly better — the `open` enumeration with category/tracker/updatedBefore filters, and notes=people dropping 12 bot comments out of 14 on issue #104460, are not things I would have built by hand with curl. But an agent that follows AGENTS.md literally never calls this tool. The repository instruction is not wrong, it is just older than the tool.

The server has no way to see this, because it only sees calls that were made.

## Query

No call — this reports a call NOT made and why. The repository file in question is typo3/AGENTS.md at the root of the TYPO3 CMS core checkout, read into context automatically by the client.

## Suggestion

If the people maintaining this server also have influence on the core's AGENTS.md, the Forge paragraph there should point at typo3_forge_lookup with the curl recipe as the fallback for sessions without the server. As it stands the repository actively routes agents away from the better tool.
