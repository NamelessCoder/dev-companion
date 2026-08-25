---
date: 2026-08-25T11:06:35+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3-core-patch-development, typo3-core-patch-checkout, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# Backporting a diff that touches Build/Sources/TypeScript: no procedure for the generated-JS conflict

## Observation

Task: rebase Gerrit change 95412, the 14.3 backport of 95392, a one-token TypeScript fix in EXT:form's form editor.

The backport had been made with Gerrit's web "Cherry pick" action, and patch set 1 came back with raw conflict markers committed into the shipped build output: markers at lines 13-17 of typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js. The file is therefore not valid JavaScript, and the whole form editor would be dead on 14.3 if it merged - a strictly worse outcome than the bug it fixes. Gerrit records this only in a change message ("Patch Set 1: Cherry Picked from branch main. The following files contain Git conflicts: * ...").

The part no page carries is what to do next, and it is TYPO3-specific rather than general git:

1. Neither conflict side may be taken. I tokenised both sides (split on comma, semicolon and braces) and diffed: 840 tokens on the 14.3 side against 880 on the main side, 562 tokens differing. The minifier mangles variables per branch - the same expression is a.querySelector(...) in 14.3's output and i.querySelector(...) in main's - and main's module carries additional code. Taking theirs would have silently shipped main's entire form-editor view model onto 14.3 while looking like a resolved conflict.
2. The generated file must not be hand-merged either, because AGENTS.md forbids editing Resources/Public/JavaScript and the correct content is whatever the branch's own toolchain emits.
3. The actual procedure is: reset the generated file to the target branch's committed version (git checkout --ours -- PATH), run CI=true ./Build/Scripts/runTests.sh -s build, then stage only the file the patch owns. On 14.3 that produced exactly one token of change against the committed output (a. becomes a?.) and touched no other generated file, which is also the proof that the resolution is right.

typo3_rule_lookup with documentId="core/contribution/gerrit-workflow" was read whole and is otherwise excellent - its "Release Branches and Backports" section is exactly the right place for this and stops one step short. It says: "A backport is a cherry-pick of the merged commit onto the release branch, usually started from Gerrit's Cherry pick action. The Change-Id of the original change is kept unchanged ... and the code is adjusted to the older branch where it no longer applies cleanly." "Adjusted" is the whole of it, and it is written as if the conflicting file were source.

typo3_test_run_guide marks -s build as runs:change and warns it rewrites the committed JavaScript. That warning is correct and it reads as a reason to avoid the suite; here the suite is the tool for the job. Nothing connects the two.

There is an open feedback from 2026-08-24 ("checkGruntClean is marked unsafe for a working checkout but no alternative is named"). This session found the alternative and it is the same one: a throwaway git worktree branched off the target branch, -s build inside it, then a token-level diff of the minified file against the branch's committed version to confirm the change is the expected one and nothing else moved. -s build runs npm inside Build/ and needs no composerInstall, so a bare worktree is enough - confirmed here on a fresh worktree.

## Query

Task, in the user's words: "kannst du mir diesen auf 14.3 rebasen 95412: [BUGFIX] Fix null access when marking invalid stage items | https://review.typo3.org/c/Packages/TYPO3.CMS/+/95412 das ist der cherry-pick". Change 95412 is the 14.3 backport of 95392; both touch Build/Sources/TypeScript/form/backend/form-editor/view-model.ts and typo3/sysext/form/Resources/Public/JavaScript/backend/form-editor/view-model.js. Calls made while working it out: typo3_gerrit_lookup(change="95412", messages="people"); typo3_rule_lookup(documentId="core/contribution/gerrit-workflow") read whole. Neither carried the step.

## Suggestion

Add a section to core/contribution/gerrit-workflow, under "Release Branches and Backports", or a guide of its own - "Backporting a Change That Touches Generated Assets" - reachable from the guides list typo3_project_describe returns. It should state:

- Which paths make a backport this case: Build/Sources/TypeScript/PKG/ paired with typo3/sysext/EXT/Resources/Public/JavaScript/, and Build/Sources/Sass/ paired with Resources/Public/Css/. A diff carrying both will conflict in the second one whenever the branches' sources have diverged anywhere in the module.
- That neither --ours nor --theirs is a resolution for the generated half, with the reason: identifier mangling is a property of the whole module, so the same source expression has different variable names on each branch, and taking the source branch's side replaces the target branch's module wholesale while presenting as a clean resolution.
- The procedure: git checkout --ours -- GENERATED_PATH to put the target branch's output back, CI=true ./Build/Scripts/runTests.sh -s build, then stage only the generated file belonging to the patch. Say to do it in a git worktree branched off the target branch so the user's checkout is untouched, and that -s build needs no composerInstall there.
- The verification, because "it built" is not it: diff the rebuilt file against the branch's committed version - tokenised, since it is one long line - and confirm the difference is exactly the expected change and that no other generated file moved. That last part is also how a stale committed build output on the target branch is discovered.
- That Gerrit's web cherry-pick commits conflict markers rather than failing, so a backport created that way is checked for markers before anything else.

typo3_test_run_guide's -s build entry should carry a whenToUse line naming this case, so the suite is not read as one to avoid.
