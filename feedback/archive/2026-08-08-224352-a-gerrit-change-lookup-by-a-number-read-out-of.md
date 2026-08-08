---
date: 2026-08-08T22:43:52+00:00
category: idea
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3_gerrit_lookup, typo3_forge_lookup
directory: /home/benji/projects/typo3-cms
---

# a gerrit change lookup by a number read out of the forge answer returns nothing new

## Observation

Task: triage old core bugs and fix one. While ruling out #82228 I fired `typo3_gerrit_lookup(issue="82228")` and `typo3_gerrit_lookup(change="53819")` in the same turn, having read 53819 out of the `reviews` array that `typo3_forge_lookup(issue="82228")` had already returned.

The two answers were identical: the same change number, subject, status ABANDONED, branch master, patch set 3, commit sha and URL. Nothing in the change-form answer was absent from the issue-form one, and the issue-form answer of `typo3_forge_lookup` had already given me the change number, the patch set and the timestamp before either Gerrit call. So the forge answer plus one Gerrit call by issue number is the whole of what three calls produced.

The waste was mine, but the descriptions invite it. `typo3_gerrit_lookup`'s `change` parameter says to prefer the Change-Id "where the commit is in front of you" and warns that a bare change number can be mistaken for a Forge issue number — which reads as guidance about which identifier to use, not as a statement that the call is redundant when the number came from a forge answer that already carried the change's state. What I actually needed the Gerrit server for was the patch's diff, and that is not something either form returns: I got it with `git fetch https://review.typo3.org/Packages/TYPO3.CMS refs/changes/19/53819/3 && git show FETCH_HEAD`, working the refspec out myself from the change number.

Reading that diff is what settled #82228 — it showed the 2017 proposal reinterpreted `width` plus `height` as a fit-into-box, which is what the `m` modifier already does, so the report was asking for existing behaviour under another spelling. That was the single most decisive read of the triage and the server had no way to give it to me.

## Query

typo3_forge_lookup(issue="82228") followed by typo3_gerrit_lookup(issue="82228") and typo3_gerrit_lookup(change="53819")

## Suggestion

Two things. Say in `typo3_gerrit_lookup`'s `change` description that where the number was read out of a `typo3_forge_lookup` answer's `reviews` array the state is already in hand, and the call is only worth making for a change the forge answer does not name. And give the answer the refspec: a change number and patch set determine `refs/changes/<last two digits>/<number>/<patchset>`, so returning that string — or the whole `git fetch <remote> <refspec>` line — turns "there was an attempt" into "here is what it did" without the caller reconstructing the sharded path. On an abandoned patch that diff is the cheapest description of what a fix looks like, which the triage skill itself says.
