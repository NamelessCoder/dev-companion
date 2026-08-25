---
date: 2026-08-24T18:34:20+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# the review skill predicts the crossing into patch development, and I still crossed without noticing

## Observation

Task: review Gerrit change 91127, test it through, and fix merge conflicts if there were any. Two skills activated and both fitted: typo3-core-patch-checkout for getting patch set 8 onto a review/91127 branch, and typo3-core-patch-review for the review itself.

typo3-core-patch-review carries a section titled "Where the review ends and the rework begins". It says: "When you are asked to make the change, invoke typo3-core-patch-development and work from it ... What asks for it is an instruction to change the patch — 'finish it', 'fix it', 'amend it', 'write the test' — and it looks like nothing at all from the inside, a sentence in a conversation in the middle of a session that is going well." It then describes a session that "edited ColumnMap.php, added a fixture column, wrote a functional test, ran seven suites and amended the commit, all still inside this skill. Nothing broke and the tree stayed clean, which is why nothing marked the crossing."

I read that paragraph and then did the same thing. The user wrote (German) "wir sollten sie hier wieder mit aufnehmen" — we should take them back in here — about three tests I had reported as skipped with a stale reason. From there I edited typo3/sysext/frontend/Tests/Functional/SiteHandling/SiteRequestTest.php (removed three markTestSkipped calls, three @todo docblocks, three @phpstan-ignore comments, added six body assertions), edited Fixtures/PlainScenario.yaml to add a page with uid 403, ran functional, cgl, phpstan, checkIntegrityPhp and lintYaml, and drafted the amended commit message. All still inside the review skill. I never invoked typo3-core-patch-development.

Two things are worth reporting about that.

First, the prediction was accurate down to the wording, and it still did not fire. What I noticed at the moment was not "am I being asked to change the patch" but "does this belong in this commit" — a scope question, which I did answer carefully and which is not the question the skill was asking me to notice.

Second, the concrete cost is not nothing. The development skill's entry conditions include the deprecation sweep (typo3_changelog_lookup with type "deprecation" at each declared major), which the base order exempts a review from and does not exempt a change from. I wrote 154 lines of new test code and a modified YAML fixture without ever running that sweep. It very probably would have returned nothing relevant for a test file — but "probably nothing" is exactly the reasoning the exemption is written to prevent, and I made it silently.

I would also note what the skill got right and should keep: the checklist's "what a dropped candidate owes" section directly changed my output. It made me record four candidates I had let go with what let each one go, rather than silently narrowing the review, and one of those — the precedent sweep for protected-to-public visibility — is what caught an incorrect answer from typo3_commit_message_guide before I reported it as a blocker. The severity rubric's "Who can reach the path raises a rank and never lowers one" also stopped me down-ranking the meta-tag leak because it looked cosmetic.

## Query

Skill typo3-core-patch-review, section "Where the review ends and the rework begins". Trigger sentence in the session was the German "wir sollten sie hier wieder mit aufnehmen" (we should take them back in here), following a review finding that three tests in SiteRequestTest.php were skipped for a reason no longer true. The files then edited were typo3/sysext/frontend/Tests/Functional/SiteHandling/SiteRequestTest.php and .../Fixtures/PlainScenario.yaml.

## Suggestion

The section is already as explicit as prose can be, so more prose will not fix it. What might: make the crossing checkable rather than noticeable. A single line at the end of the section — "before your first Edit or Write to any file, ask whether typo3-core-patch-development should be running instead" — attaches the check to an action that is observable from the inside, rather than to recognising an instruction, which by the skill's own account is what fails.

The other half is that the two skills overlap in what they permit. The review skill explicitly allows a scratch probe that writes files and restores them. That is right and I used it. But it means "I am writing files" is not by itself the signal, and the section does not say where the probe ends and the rework begins. A sentence distinguishing them — a probe is restored and leaves no diff, a change is meant to survive — would give the check a shape.
