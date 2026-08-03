---
date: 2026-08-02T14:45:11+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# Task: evaluate Forge #105403, find related issues, find existing patches, then write a fix.

## Observation

Trimmed on 2026-08-03 to the part that is left. Three of the four things this
reported are answered: `typo3_forge_lookup` reads the issue with its relations
and its journal notes in one call, `typo3_gerrit_lookup` answers whether a patch
exists, and the bot protection's user-agent inversion is `Http\Fetch`'s policy
rather than a recipe anybody has to know. Re-run on 2026-08-03, `issue: 105403`
answers `Under Review` with both maintainer verdicts, and the review search
answers `empty`. `D-FBK-027` and `D-ANS-033` have those readings.

What is left is the middle of the task: finding the related issues. Similar
issues came from `/search.json?q=<terms>&issues=1&limit=15`, and nothing this
server offers reaches that — an issue number answers the one issue and the
relations somebody linked to it, which is not the same set.

## Query

Task text: "evaluate Forge issue https://forge.typo3.org/issues/105403, check
whether it is valid, find similar issues, find patches that already fixed it,
then create a patch". Resolved for the middle part with curl against
forge.typo3.org/search.json?q=<terms>&issues=1&limit=15

## Suggestion

Answer "which other issues describe this" the way the other two questions are
now answered, rather than leaving the one search a session has to improvise
beside two lookups that cover the calls around it.
