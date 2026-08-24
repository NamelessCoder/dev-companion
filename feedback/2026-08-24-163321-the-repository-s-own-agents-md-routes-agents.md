---
date: 2026-08-24T16:33:21+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_forge_lookup, typo3_commit_message_guide, typo3_changelog_lookup
directory: /home/benji/projects/typo3-cms
---

# The repository's own AGENTS.md routes agents around three tools that answer better

## Observation

Task: search Forge for issues in the asset renderer area, fix one, write its changelog and commit message. Working in the TYPO3 CMS core monorepo, which ships an AGENTS.md read into context at session start.

That AGENTS.md competes with this server on three points, and in each case it named a worse route:

1. Forge. AGENTS.md says: "Issue tracker: forge.typo3.org (Redmine). Query it through the JSON API with curl -H 'Accept: application/json' and the default curl user agent — browser-like user agents hit the Anubis bot check. Useful endpoints: issues/<id>.json (add ?include=journals for comments), search.json?q=<query>, issues.json?project_id=27". I used typo3_forge_lookup instead and it was clearly better — the `open` enumeration with category/tracker/updatedBefore filters, and notes=people dropping 12 bot comments out of 14 on issue #104460, are not things I would have built by hand with curl. But an agent that follows AGENTS.md literally never calls this tool. The repository instruction is not wrong, it is just older than the tool.

2. Commit message. I never called typo3_commit_message_guide. I wrote the message from AGENTS.md's rules (72-char limit on every line including trailers, Resolves/Releases/Signed-off-by order, omit Change-Id because the hook adds it). Those rules held — the message I produced was accepted by the user and the Change-Id survived an amend. So AGENTS.md was sufficient here, which is exactly why the tool never got called.

3. Changelog. I never called typo3_changelog_lookup. I derived the Important-*.rst format by cat-ing typo3/sysext/core/Documentation/Changelog/14.3/Important-109585-*.rst and copying its shape — including the ..  _important-<issue>-<timestamp>: label, which I would not have guessed. I got the headline underline length wrong on the first attempt (72 vs 71) and had to fix it in a second pass.

The pattern: where AGENTS.md gives a rule that is good enough, the tool is never reached; where AGENTS.md gives a route that is worse (curl), an obedient agent takes the worse one. The server has no way to see any of this, because it only sees calls that were made.

I did not verify whether typo3_changelog_lookup would have given me the Important-file skeleton — its description is about "what a version broke, deprecated or added", which reads as consuming changelogs, not authoring one. So I did not try it. That may be another naming miss rather than a gap.

## Query

No call — this reports three calls NOT made and why. The repository file in question is typo3/AGENTS.md at the root of the TYPO3 CMS core checkout, read into context automatically by the client.

## Suggestion

Two things worth considering.

First, if the people maintaining this server also have influence on the core's AGENTS.md, the Forge paragraph there should point at typo3_forge_lookup with the curl recipe as the fallback for sessions without the server. As it stands the repository actively routes agents away from the better tool.

Second, and independent of that: typo3_changelog_lookup's description sells it as a reader of past changelogs ("what a version broke, deprecated or added, on a major you have not built on lately"). Authoring a new entry is a different job and, judging by this session, the more common one for an agent in this repository — every user-facing core patch needs one. If it can hand back the skeleton for a given type (Breaking/Deprecation/Feature/Important) with the required sections, the reference-label format and the underline rule, say so in the description; I would have called it. If it cannot, that skeleton is worth adding somewhere, because I got the underline length wrong on the first try and only caught it by measuring the strings myself.
