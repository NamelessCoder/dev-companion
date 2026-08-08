---
date: 2026-08-08T22:43:54+00:00
category: tool-gap
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup
directory: /home/benji/projects/typo3-cms
---

# typo3_gerrit_lookup returns the patch set but not the refspec to fetch it with

## Observation

Task: review Gerrit change 95179 ("[BUGFIX] Let stdWrap override apply the value 0") in a git worktree of a TYPO3 core checkout, at the user's request.

typo3_gerrit_lookup(change "95179") answered with everything that identifies the patch set: number 95179, subject, status NEW, branch main, patchSet 1, commit 0b18ff0af75d3dbae5a28f92d0abf9a4a1be7870, project Packages/TYPO3.CMS, url. That commit hash was the single most valuable thing in the session. It is what let me state that the checkout under review is the revision on the server rather than an older patch set, which the checkout itself cannot report.

What it did not return is the one string needed to act on the answer: the fetch refspec. I constructed refs/changes/79/95179/1 myself, from prior knowledge that Gerrit shards by the last two digits of the change number, zero-padded, then the change number, then the patch set. Every input to that string was already in the answer (number and patchSet), and the project field even gives the remote path. A client without that piece of Gerrit trivia holds a complete description of a patch set it cannot fetch, and the obvious next moves (guessing at a branch name, or cloning the review server URL) do not work.

This is the step I worked out for myself and would work out again next session. It is small, it is mechanical, and it is the join between "which patch set is current" and "get it onto disk".

## Query

typo3_gerrit_lookup(change: "95179")

## Suggestion

Add the fetch refspec to each change entry, for example "fetchRef": "refs/changes/79/95179/1", and ideally the whole command: git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/79/95179/1. Both are derivable from fields already in the answer (number, patchSet, project), so nothing new has to be fetched. Where an older patch set is asked for, the refspec should follow that patch set number rather than the current one.
