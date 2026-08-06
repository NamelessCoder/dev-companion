---
date: 2026-08-05T22:05:50+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_commit_message_guide
directory: /home/benji/projects/typo3-cms-mcp
---

# Three properties of get.typo3.org that decide what a re-read finds

## Observation

Task was the todo "hold the release lines a commit message claims", which asked for the maintained TYPO3 branches to be read from a source that stays current and written into knowledge/.

get.typo3.org is that source and now backs knowledge/release-lines.json, but three of its properties will trip the next session that re-reads it, and none is written down:

1. /api/v1/major/ has no entry for the development line. /api/v1/major/15 is a 404 while main is at 15.0.0-dev, so the branch every core change targets first is the one branch the API cannot supply. It is stated in release-lines.json rather than read, and a re-read that trusts the API alone would drop it.
2. The `subtitle` field is stale in the direction that matters. Major 14 still reads "The upcoming LTS release (for new projects)" months after its release_date of 2026-04-21 passed, while major 13 reads "The stable LTS release". A session taking the prose for the state gets the current LTS backwards. Only maintained_until and elts_until are load-bearing.
3. /api/v1/release/ carries non-semver version strings — "7-snapshot-20170404" among them — so anything that parses or sorts that list has to filter first. Mine crashed on it.

The checkout confirms the API rather than replacing it, which is worth recording as the cross-check: a line stops receiving commits when regular support ends. origin/12.4 last moved 2026-04-14 against a maintained_until of 2026-04-30, origin/11.5 on 2024-10-16 against 2024-10-31.

## Query

curl https://get.typo3.org/api/v1/major/ , curl https://get.typo3.org/api/v1/major/15 (404), curl https://get.typo3.org/api/v1/release/ , then `git -C <core-checkout> log -1 --format='%ci %s' origin/<branch>` for main, 14.3, 13.4, 12.4 and 11.5.

## Suggestion

Write the three beside knowledge/release-lines.json, or into D-ANS-058, so the next re-read does not rediscover them — the file names its source and the day it was read, which is what invites a re-read, and none of the three is visible from the endpoint without hitting it. The development line being absent is the one that changes an answer rather than costing a round trip.
