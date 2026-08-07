---
date: 2026-08-07T13:25:59+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# the review skill has no marker for the point where review turns into rework, and does not name th...

## Observation

Task: "gib mir ein review auf [BUGFIX] Handle empty dates consistently in Extbase, sei kritisch, prüfe alles" — review a local core commit against a v15 checkout. Later in the same session the user chose a scope from my findings and asked me to implement it and amend.

The skill fitted. Its order — establish the patch, then the rules that judge it, then read the checkout — was the right order and I would follow it again. Two parts earned their place concretely. First, "enumerate what the diff removes or renames before asking": working the deletions rather than the additions is what let me conclude with evidence, and quickly, that no changelog entry, no extension scanner matcher and no [!!!] were owed, since the only deletion was an unused use-statement. Second, the dropped-candidate rules, in particular "Unlikely is not disproved. What disproves a path is what makes it impossible." That is what pushed me from "comparing a native date column against 0000-00-00 would presumably fail on PostgreSQL" to actually running it and pasting SQLSTATE[22008]. That single instruction produced the finding that stopped the patch.

The gap is the exit. The skill closes with "Where the answer is that the patch needs work, name it and stop — typo3-core-patch-development owns making the change, the changelog entry, the tests it needs and the push", and states "It does not change the patch: a review that rewrites what it reviews has destroyed the evidence for its own findings." In this session the user read the review, picked a scope, and asked for the change. I went on to edit ColumnMap.php, add a fixture column and a model property, write a new functional test, run seven suites and git commit --amend — all still holding the review skill's instructions, and I never activated typo3-core-patch-development. Nothing broke and the tree stayed clean, but there was no moment at which anything marked the handoff, and the skill describes the boundary as sharp while in practice it is a sentence in a conversation.

Second thing the skill does not describe, and which was the highest-yield technique I used: adding a temporary column to a test fixture, running a targeted functional test against it as a probe, reading the output, then reverting with git checkout --. I did this four times. It converted three findings from reasoned to measured, including one — that neither Extbase nor DataHandler can write an empty value into a dbType=date nullable=false column, both failing with NOT NULL constraint failed — that no amount of reading would have produced. The skill permits "A review may run what cannot change the code", which strictly read forbids this, since the probe does change files before restoring them.

## Query

Skill(typo3-core-patch-review) args="HEAD — [BUGFIX] Handle empty dates consistently in Extbase"; session then continued into editing ColumnMap.php, writing a functional test, and git commit --amend, without typo3-core-patch-development being activated.

## Suggestion

Say in typo3-core-patch-review what the handoff looks like from the inside: once the reader accepts findings and asks for the change, the session activates typo3-core-patch-development rather than continuing under review rules, and that includes the amend and the push. A caller who reached the review skill from a conversation will otherwise carry review constraints into development work.

And consider naming the scratch-probe as a legitimate review method, with its condition: a review may add a temporary fixture or column and run a targeted suite against it, provided the working tree is restored and the restoration is verified, because that is the whole difference between "this would presumably throw" and a pasted SQLSTATE. As written, "may run what cannot change the code" reads as forbidding it.
