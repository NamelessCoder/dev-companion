---
date: 2026-08-10T10:17:51+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3-core-patch-review, typo3_rule_lookup, typo3_forge_lookup, typo3_gerrit_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# what carried this review: the scratch-probe permission, the changelog-per-type rule, and pairing ...

## Observation

Task: review a core patch ([TASK] Sticky language header in comparison view, Forge #110403, Gerrit 95178) and then rework it. Filed as the counterpart to my other findings from this session, because these are the parts that must not be broken.

typo3-core-patch-review fitted the task and I would change nothing in it. Two of its instructions did the heavy lifting. First, the permission to run a scratch probe and put the tree back: I added throwaway tests under Build/Sources/TypeScript/backend/tests/, ran the unitJavascript suite against them, read what they printed, deleted them and confirmed git status clean. That turned two reasoned findings into measured ones (a residual inline visibility:hidden surviving the leave transition; a wrapper collapsing 19.11px to 0 when the element went position:fixed) and, more valuably, refuted one of my own: I had predicted the @starting-style entry animation was dead code, and the probe measured opacity 0.135 one frame after the class change, so the transition does run. Second, the dropped-candidate rule made me write down what disproved each candidate instead of quietly dropping it; three of the five I dropped were disproved by a lookup rather than by taste.

typo3_rule_lookup with one query covering changelog and release targets returned both sections whole and was the highest-value single call. "A BUGFIX owes none, a TASK owes none" stopped me reporting a missing changelog entry, which the skill's own checklist calls a review defect. The Release Targets section then warned that a plain log on a release branch and origin/main..origin/14.3 give opposite answers about whether features reach a release line — "the same count that is 0 one way is 188 the other" — so I used the right operator and got 0 [FEATURE] out of 338 commits, which is the evidence the backport finding rests on.

typo3_forge_lookup and typo3_gerrit_lookup pay off as a pair. Gerrit gave patch set 4 and its commit hash, which I held against git rev-parse HEAD to prove I was reviewing the current patch set rather than an older one. Forge gave the reviewer comment on patch set 3 asking for clipping at the container edge and a softer transition — already answered by patch set 4 — so I did not re-report a finding somebody had already made and the author had already fixed.

Three hints fetched by id were each worth a finding or a dropped candidate: css-z-index-layering confirmed calc(token ± n) is the established idiom, so an ad-hoc-looking z-index became a dropped candidate instead of a report; css-motion-transitions produced a rule-backed prefers-reduced-motion finding I would otherwise have rated optional; css-color-surface-tokens ("Do not jump to a higher surface to win contrast — that is what a border, a shadow or a state token is for") corrected my instinct when the developer said the styling was indistinguishable in both schemes, and the fix became the component border and flyout shadow rather than a brighter background.

typo3_commit_message_guide with workflow="core" was the right shape three times over: it confirmed 14.3 is a maintained line without me having to reason about support windows, and normalised the trailer order, while leaving the judgement calls it cannot see — TASK versus FEATURE against a diff that registers a new web component — to me.

## Query

typo3-core-patch-review on HEAD; typo3_rule_lookup query="changelog entry feature important task release branches backport"; typo3_forge_lookup issue=110403; typo3_gerrit_lookup change=<Change-Id from the commit message>; typo3_hint_lookup ids css-z-index-layering, css-motion-transitions, css-color-surface-tokens

## Suggestion

Keep the scratch-probe paragraph and the dropped-candidate section of typo3-core-patch-review verbatim; they are what separated measurement from assertion in this session. Keep typo3_rule_lookup returning whole sections for a multi-subject query rather than snippets. Keep the forge/gerrit pairing as the way a review establishes which patch set it is looking at.
