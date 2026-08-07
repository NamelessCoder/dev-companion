---
date: 2026-08-07T23:34:31+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms-mcp/.checkouts/main
---

# typo3-core-issue-triage promises "deciding whether a report is worth taking on" but carries no st...

## Observation

Task: 30 oldest unresolved core issues, verify the first genuine bug against a 15.0.0-dev checkout. The user's closing words were "I want to know what I would be signing up for before I touch it."

Forge 15984 has a history that is unusual but not rare: it was fixed in 2011 (Gerrit 1186 merged to master, 2545 to TYPO3_4-5) and the fix was reverted across all branches in March 2012, because it caused the memory blowup reported as issue 32756 — 537 pages producing 10,330 queries and needing a 1 GB memory limit, because the patch put a rootline walk into the shared checkPageGroupAccess() that getTreeList() also called.

The verdict "Still happens" came out of the skill's order cleanly. The part the user actually asked for — whether the fix can be retried and what review will demand of it — was mine to work out, and I would work it out the same way next session:

1. grep for production callers of the naive check, TYPO3\CMS\Core\Domain\Access\RecordAccessVoter::groupAccessGranted. Exactly one outside its own class: PageLinkBuilder.php:486. So a fix scopes to one call site rather than to a shared path.
2. read whether the path that blew up in 2012 still routes through it. PageRepository::getDescendantPageIdsRecursive (PageRepository.php:2003) now calls accessGrantedForPageInRootLine per row as it descends, top-down — which is precisely the redesign Jigal van Hemert proposed on issue 32756 in January 2012 — and it does not touch groupAccessGranted at all.
3. check whether the cost that justified the revert still stands. RootlineUtility now has both a persistent "rootline" cache and a runtime cache, which it did not in 2011.

That is a repeatable procedure, and it is knowledge rather than checkout state: the blast radius of a shared access-check method is what decides whether a reverted patch can be re-attempted. The general form is that a reverted core fix becomes re-attemptable when the shared consumer that made it expensive has since been rebuilt, or when the caller set has shrunk to the one site the fix actually needs.

The skill's description claims this ground — "for deciding whether a report is worth taking on, and for saying what a maintainer would need before it can move" — but its body stops at the verdict and hands everything else to typo3-core-patch-development. There is no step between "still happens" and "write the patch".

At the moment such a step would have had to fire, I had issue 32756's notes open and PageLinkBuilder.php, RecordAccessVoter.php and PageRepository.php open in the checkout, and the question in my head was "is the thing that killed this last time still true".

## Query

Session task: triage the oldest unresolved core bugs; the user's own words were "I want to know what I would be signing up for before I touch it." Issue triaged: Forge 15984.

## Suggestion

Add a step to typo3-core-issue-triage between the verdict and the handoff, firing where an issue's relations or notes show a merged-then-reverted fix: read the revert reason out of the related issue, find every production caller of the method the reverted patch touched, and establish whether the path named in the revert still routes through it. State the general form so it transfers — a reverted core fix is re-attemptable when the shared consumer that made it expensive has been rebuilt or the caller set has shrunk to the one site the fix needs. Either that, or narrow the skill description so it stops promising the feasibility assessment it has no procedure for.
