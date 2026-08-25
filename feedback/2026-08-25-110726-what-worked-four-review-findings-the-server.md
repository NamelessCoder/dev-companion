---
date: 2026-08-25T11:07:26+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_task_guide, typo3_test_run_guide, typo3_commit_message_guide, typo3-core-patch-review
directory: /home/benji/projects/typo3-cms
---

# What worked: four review findings the server killed before I reported any of them

## Observation

Task: review core patch 95392, a one-token null-guard in Build/Sources/TypeScript/form/backend/form-editor/view-model.ts plus its generated JavaScript.

Reporting the failures is easy to remember and the saves are not, so here are the four, each with the candidate it killed and the wording that killed it. All four were on my list before the call and off it after.

1. typo3_rule_lookup(query="changelog entry bugfix") - I was going to ask for an Important entry, on the reasoning that a backend editor becoming unusable is user-facing. The answer's "Which Change Owes a Changelog File" section says a BUGFIX owes none unless it changes what an installation renders, is configured by, or has documented, and then says the quiet part out loud: "Demanding one of a BUGFIX that changes none of the three is a review defect of its own." That last sentence is what made me drop it rather than hedge. Two subjects in one query, one call, settled.

2. The backend-typescript hints that came back inside typo3_task_guide - I had (stageItem as any).invalid = true flagged as a no-explicit-any violation. The hint says "@typescript-eslint/no-explicit-any and @typescript-eslint/no-inferrable-types are off on purpose, so neither is a finding - imitating a neighbouring file turns a rule that is switched off into a diff nobody asked for." Dropped. It also named Build/eslint.config.mjs as the authority, which is what let me say "what it does not configure is untidiness rather than a lint failure" instead of arguing style.

3. typo3_test_run_guide's checkGruntClean entry - the suite that answers exactly the question I had (does the committed JavaScript still match its source), and its whenToUse says its body "runs git add * over the whole working tree, so it stages every file the checkout holds, untracked ones included. Run it in a checkout whose index you can throw away, and not in one holding work of your own." I was about to run it in the user's checkout. I answered the question by tokenising the minified file and diffing instead, and named checkGruntClean as not run with that reason. This is the concrete disaster the server prevented.

4. typo3_commit_message_guide(workflow="core") - I would have guessed the subject limit. It returned summary-length-preferred with the arithmetic ("57 characters long: a 48-character summary plus 9 for the keyword prefix"), which let me rank it as a preference rather than a violation instead of asserting a number I half-remembered.

One more that is structural rather than a single answer: typo3_project_describe ends with a guides array of 19 documentIds, and that is the only place a client rendering no resource list learns those pages exist. In the second half of the session I read core/contribution/gerrit-workflow whole off that list, and its "Pushing From a Git Worktree" and "Release Branches and Backports" sections were exactly my case. Without the array I would not have known to ask for the page by id. There are two open feedbacks complaining that the guides list goes unread; this session it was read and it paid.

## Query

Session task: "bitte review mir 95392: [BUGFIX] Fix null access when marking invalid stage items". Calls whose answers removed a candidate finding: typo3_rule_lookup(query="changelog entry bugfix", targetVersion="15"); typo3_task_guide(changeType="audit", paths=[the two changed files], targetVersion="15"); typo3_commit_message_guide(workflow="core", message=..., changeType="BUGFIX"); typo3_test_run_guide(paths=[the two changed files], targetVersion="15").

## Suggestion

Keep all four wordings as they are. The pattern common to the three that worked best is that each states the negative case explicitly - what is not a finding, what the rule does not demand, what the suite will do to you - rather than only stating the rule. "Demanding one of a BUGFIX that changes none of the three is a review defect of its own" and "neither is a finding" are doing more work than the rules they sit next to, because a review's failure mode is over-reporting and only a sentence aimed at that stops it.

If that pattern is worth spreading: the other rule and hint sections a review consults would benefit from the same one-line negative. For example the public-api-surface hint could say which removals are not breaking, and the testing hints could say which changes owe no test.
