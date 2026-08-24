---
date: 2026-08-24T18:34:47+00:00
category: idea
status: open
model: claude-opus-5
tool: typo3_rule_lookup, typo3_forge_lookup, typo3_project_describe
directory: /home/benji/projects/typo3-cms
---

# what worked and must not be broken: the gerrit-workflow page, and forge_lookup surfacing the aban...

## Observation

Task: review Gerrit change 91127 against a 15.0.0-dev core checkout, test it through, fix merge conflicts if any. Filing this because the debrief asked to be as concrete about what worked as about what failed.

1. typo3_rule_lookup(documentId "core/contribution/gerrit-workflow"), read whole, one call. This prevented a concrete dead end. My instinct was `git fetch origin refs/changes/27/91127/8`, and the checkout's remote is git@github.com:TYPO3/typo3.git for fetch with a Gerrit ssh URL only as pushurl. The page states it directly: "The ref is on Gerrit and not on GitHub. A core clone fetches from the mirror and pushes to the review server, so `git fetch origin refs/changes/...` reports that the ref does not exist in a checkout whose push would reach it", with the measurement date beside it. It also gave me the https URL that serves change refs without an account, the `git switch -c review/<number> origin/main` form with the reasoning for why the carry must not land on main itself, and the `git branch -D` undo with the note that git refuses the ordinary deletion. I used all four. The document carried the procedure end to end and never sent me back to a search. This is the model of what a whole-document read should be.

2. typo3_forge_lookup(issue 103215) surfaced something the patch and its commit message do not contain: the `reviews` array listed change 85224 alongside 91127. Reading 85224 with typo3_gerrit_lookup gave me Benjamin Franzke's comment "we should rather fix the page renderer state (intermediate fix would be to use the existing update/set state methods), rather than parking the entire container. -1 for now", and Christian Weiske's two regression reports that killed it. That is the argument that made 91127 defensible — it is the intermediate fix a core team member asked for — and it appears nowhere in the change under review. Without it I would have reported "making a protected reset() public is a smell" as a design finding, which would have been wrong and would have cost the author another cycle.

3. typo3_project_describe told me kind "core-checkout", typo3Version 15.0.0-dev, extensions [] — which meant step 2 of the base order (typo3_extension_describe) was answered by the absence and I could say so rather than skip it silently, and it fixed the changelog folder as 15.0 before I looked for one.

4. typo3_gerrit_lookup(change 91127) returned patchSet 8 with commit 1ac00b0e5f5 and the fetch ref refs/changes/27/91127/8. I fetched that rather than defaulting to something, and could state in the review which revision the findings are about. The comment threads came with unresolved flags, which is how I found the still-open functional-test question from 2025-12-09 that the whole review then answered.

One place an answer was right and stopped a step short, which belongs here rather than as a complaint: typo3_gerrit_lookup gave me commentCount 5 and each thread's unresolved boolean, but not which thread is the one holding the change up. I worked that out from the thread structure — a top-level comment flagged unresolved with two resolved replies under it. That is a reading the data supports and the answer does not make.

## Query

typo3_rule_lookup(documentId: "core/contribution/gerrit-workflow"); typo3_forge_lookup(issue: "103215", notes: "people"); typo3_gerrit_lookup(change: "85224"); typo3_gerrit_lookup(change: "91127"); typo3_project_describe. All against the core checkout at /home/benji/projects/typo3-cms, TYPO3 15.0.0-dev.

## Suggestion

Keep all four as they are. Specifically: keep the measured, dated statements in the gerrit-workflow page ("Measured on 2026-08-05: refs/changes/02/95102/2 resolved over the Gerrit URL and returned nothing at all over the GitHub one") — that form is what made me trust it over my own habit without testing it first. Keep the `reviews` array on a forge issue and the comment threads on a gerrit change; those two together are what let a reviewer reconstruct why a patch looks the way it does.

The one addition worth considering: on a change with several comment threads, say which are unresolved *and* top-level, or mark the thread a reader should answer. The flag is there; the ranking is the step I took myself.
