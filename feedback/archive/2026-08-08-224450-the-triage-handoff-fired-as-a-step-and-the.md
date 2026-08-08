---
date: 2026-08-08T22:44:50+00:00
category: idea
status: closed
closed: 2026-08-08
model: claude-opus-5[1m]
tool: typo3-core-patch-development, typo3-core-patch-review, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# the triage handoff fired as a step and the review handoff did not fire as prose

## Observation

Task: "bitte suche dir ein altes issue und fixe es" — find an old issue and fix it. Two handoffs between skills were in play and exactly one of them happened, and the difference is how each is written.

typo3-core-issue-triage writes its handoff as an imperative step with a paragraph explaining why it is one: "When the verdict is that it still happens and you are asked to fix it, invoke typo3-core-patch-development before making the change. That is a step, not a note about ownership", followed by an account of a session that held the same paragraph and reconstructed forty turns of the workflow instead. It fired. I invoked the skill by name at the moment the verdict turned into a patch, and got from it the changelog obligation, the release-target reading, the Gerrit lookup for an existing patch and the commit-message check — all of which I would otherwise have decided myself, and one of which (the `breaking-not-assessed` line from typo3_commit_message_guide) is what caught a breaking API change in my draft.

typo3-core-patch-development writes the other handoff as ownership prose: "it does not own judging its own: where the request is to say what is wrong with a change rather than to make one, typo3-core-patch-review owns that". It did not fire. I finished a push-ready patch — three files, two new functional tests, a commit message — ran the project's checks, and reported it without ever invoking typo3-core-patch-review, because the sentence reads as a boundary about who owns what rather than as a step I owe before handing the work over. The skill's own description does say "your own before you push it", so it was findable; nothing at the point of finishing pointed at it.

The observed pattern: a handoff written as "invoke X before doing Y" fires, and a handoff written as "X owns that" does not, in the same session, in the same model, twenty turns apart.

## Query

Skill(typo3-core-issue-triage) → verdict "still happens" → Skill(typo3-core-patch-development) → patch finished, never Skill(typo3-core-patch-review)

## Suggestion

Give typo3-core-patch-development a closing step in the same imperative form its predecessor uses: after the checks pass and before the patch is handed over or pushed, invoke typo3-core-patch-review on the diff, and carry its findings back as a work list — which is what the last paragraph already says to do with them. The ownership sentence can stay; it is not doing the work of making the step happen. Worth checking the other skills for handoffs phrased as ownership rather than as an act, since this session is one datum for the pair being distinguishable in effect.
