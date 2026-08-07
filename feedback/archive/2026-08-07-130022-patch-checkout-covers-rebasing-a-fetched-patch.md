---
date: 2026-08-07T13:00:22+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# patch-checkout covers rebasing a fetched patch, not the commit you wrote yourself

## Observation

Task: after committing the patch for Forge 109572, the user said "bitte rebasen" — my own commit sat two commits behind origin/main and was about to be pushed to Gerrit.

No skill activated. typo3-core-patch-checkout exists and its description does contain "rebase it where the branch has moved under it" and "restore the checkout to a clean current branch", but every noun around those phrases is about a change fetched from review.typo3.org: find the change, fetch the patch set, put it on the branch it targets, try out somebody's patch. I read it as being about someone else's patch and did not open it, and on that description that was the correct reading.

So I worked the sequence out myself and would work it out again: stop the running functional suite first, because runTests.sh mounts and reads the working tree and rebasing under it invalidates the run; remove the leftover functional-, memcached-func- and redis-func- containers; git fetch; inspect the intervening commits for overlap with the patch (they touched extbase/Classes/Service/ImageService.php, so the overlap was worth checking); git rebase origin/main; confirm the Change-Id survived; re-run every check on the new base. The Change-Id step is the one with consequences: if a rebase drops it, the push opens a second Gerrit change instead of a new patch set on the existing one.

## Query

no skill invoked. The request was the two words "bitte rebasen", with a committed change at fda437f2449 two commits behind origin/main and a full core functional suite running against the same working tree.

## Suggestion

Either widen typo3-core-patch-checkout's description to say it also covers a change you committed yourself that origin/main has since moved under, or give typo3-core-patch-development an explicit rebase-before-push step. Whichever carries it should state the two non-obvious parts: a running runTests.sh suite reads the mounted working tree, so a rebase under it silently invalidates the run and its containers must be cleared first; and the Change-Id must be confirmed after the rebase, because losing it turns a new patch set into a new change.
