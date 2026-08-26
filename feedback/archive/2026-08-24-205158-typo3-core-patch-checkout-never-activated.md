---
date: 2026-08-24T20:51:58+00:00
category: idea
status: closed
closed: 2026-08-26
model: claude-opus-5[1m]
tool: typo3-core-patch-checkout, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# typo3-core-patch-checkout never activated across three separate patch fetches in one session

## Observation

Task: "we want to work on almost ready small open reviews and finish them", TYPO3 core checkout.

typo3-core-patch-checkout exists, its description names exactly what I did three times, and it stayed shut for the whole session. I fetched patches out of review.typo3.org into the user's working checkout on three separate occasions and invoked no skill for any of them:

1. Eight changes at once (93151, 94712, 95179, 95368, 95393, 95394, 95396, 95399), tagged gr/<number>. This is the one that went wrong twice over — first because I built the fetch from GET /changes/<n>/revisions/current/review, which needs a credential, returned nothing parseable, and left me with eight tags all pointing at the same wrong FETCH_HEAD; then because the user objected to the fetching itself ("du hast viele meiner eigenen patches gezogen", later "keine neuen holen").
2. Change 95392 patch set 3, to run lintTypescript against it.
3. Change 93079 patch set 3, onto branch review/93079, then rebased 269 commits onto origin/main.

The moment one would have had to activate is the first user message. I was holding no files, the request was seven words, and my next action was raw curl plus git fetch.

What makes this a sharper finding than "I forgot": I invoked typo3-core-patch-review for change 95392 and worked through it. That skill contains the sentence "Fetching somebody else's patch out of review and putting it on a branch is a different job and belongs to typo3-core-patch-checkout." I read that sentence, and then in the same session fetched two more patches without invoking it. The pointer is written as a note about ownership, in a paragraph about rebasing, near the end of a long file. The one that did fire for me in the same skill — "When you are asked to make the change, invoke typo3-core-patch-development and work from it" — is written as an imperative step with the skill name in bold, and I acted on it within one turn.

The concrete cost of no checkout skill: I worked out for myself, and would work out again, that refs/changes/<last two digits>/<number>/<patch set> is the refspec, that /changes/<n>/revisions/current/review is credentialed while /changes/?q= is not, and — during the cleanup the user then asked for — that testing a local branch tip for membership in a change's ALL_REVISIONS map is what separates a recoverable branch deletion from an unrecoverable one. Three of the user's branches turned out to have Change-Ids that resolve to nothing on the server, one of them prefixed [SEC].

## Query

Opening request, verbatim: "we want to work on almost ready small open reviews and finish them". Skills actually invoked in the session: typo3-core-patch-review (args naming change 95392), then typo3-core-patch-development (args naming change 93079). typo3-core-patch-checkout and typo3-core-issue-triage never invoked.

## Suggestion

In typo3-core-patch-review, promote the pointer from a closing note to a step at the point of use, in the same imperative form as the patch-development handover, and put it where the reading starts rather than where it ends: something like "Before fetching anything, invoke typo3-core-patch-checkout." The development handover fired for me and the checkout one did not; the difference between them is form and position, not content.
