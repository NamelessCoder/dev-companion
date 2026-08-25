---
date: 2026-08-25T11:47:14+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_forge_lookup lost to the raw curl recipe that the core's AGENTS.md documents

## Observation

Task: check whether ./Build/Scripts/runTests.sh -s cglGit runs correctly inside a git worktree of the TYPO3 core checkout, fix it, and write the commit.

I needed Forge twice in this session and went around the server both times, without ever considering it.

1. Searching for an existing issue before opening a new one. I ran curl against search.json?q=runTests+worktree (empty), then widened to q=worktree&limit=25 and got {"results":[],"total_count":0}. Two calls, because the first returned nothing and I could not tell from that whether Forge was unreachable, the bot check had eaten me, or there genuinely was no issue — so the second call was purely to prove the endpoint answered at all (I checked the HTTP status separately: 200).

2. Verifying the issue number the user gave me (110534). curl against issues/110534.json, parsed with an inline python one-liner to print project, tracker, status, subject.

typo3_forge_lookup was in my tool list the whole time. The reason I never reached for it is concrete and I think worth knowing: AGENTS.md, committed in the core, documents the curl route in detail — the endpoints (issues/<id>.json, search.json?q=, issues.json?project_id=27), the Accept header, and a warning that browser-like user agents hit the Anubis bot check. That is a recipe. When the repository hands an agent a working recipe with gotchas already solved, the agent uses the recipe. The MCP tool would have had to beat a documented, pre-debugged path, and its name alone did not.

The assumption I made and never tested: that a lookup tool would give me a curated or cached view rather than the live tracker, and that for "does an issue exist right now" and "is this number real" I wanted the live tracker. I do not know whether that assumption held.

Cost of going around it: 3 curl calls plus 2 inline python parsers, and the first search returning nothing usable, which I then had to disambiguate with a second call.

## Query

Never called. Instead: curl -s -H "Accept: application/json" "https://forge.typo3.org/search.json?q=runTests+worktree&limit=15", then ...?q=worktree&limit=25, then ...issues/110534.json

## Suggestion

Two options. Either make the description say plainly what curl cannot do — "searches and reads forge.typo3.org issues without the bot check, and reports whether the tracker answered at all" would have beaten the recipe for me, particularly the last clause, since disambiguating "no results" from "no answer" cost me a whole extra call. Or, if the intent is that agents use the documented curl route in a core checkout, say so in typo3_server_scope's doesNotCover so the tool is not sitting there unexplained. Worth raising with the core team as well: AGENTS.md teaching a curl recipe for a service this server also fronts means every agent in this repository will do what I did.
