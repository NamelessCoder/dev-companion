---
date: 2026-08-04T01:26:44+00:00
category: tool-gap
status: open
model: claude-opus-5
tool: typo3_commit_message_guide, typo3-extension-conformance, typo3-content-element-development, typo3-extension-testing
directory: /home/benji/projects/syntax
---

# Task: fix a reported frontend defect in the extension this session was standing in and commit it,...

## Observation

Task: fix a reported frontend defect in the extension this session was standing in and commit it, a patch release to follow. Driven non-interactively in /home/benji/projects/syntax (bk2k/syntax 5.0.0, TYPO3 14.3.0, DDEV), with this server on stdio and the nine skills it publishes in .claude/skills/. The run was made to see whether an agent committing in an extension repository passes workflow="project" to typo3_commit_message_guide, which D-GUI-002 rests on.

It never reached the question. 37 tool calls, every one of them Bash, Read, Edit or Write; not one call to any of the 26 tools this server offered, and no skill activated. Both were in the session's context: the tool list carries typo3_commit_message_guide and the skill listing carries all nine with their descriptions. The session read both setup.typoscript files, grepped the shipped prism-plugin-showlanguage.min.js for the warning text the report quoted, started DDEV, drove a headless Chromium at the frontend to read the emitted script order and find the empty .code-toolbar wrapper, swapped two lines in Configuration/Sets/Base/setup.typoscript, re-rendered to see the "PHP" caption appear, ran the repository's own composer test and composer cgl:ci, and committed.

The message that landed (bc0946c) is "[BUGFIX] Load Prism toolbar plugin before show-language" over a wrapped body, with no Releases:, no Forge issue trailer and no Change-Id - the answer R-AUD-003 and R-GUI-002 exist to produce, arrived at without this server being asked for it. The argument's default was therefore never in play. Called with changeType and summary and no workflow, the guide still answers with Resolves: #ISSUE_NUMBER, Releases: RELEASE_TARGET and a hard "A Forge issue is required", re-measured over stdio against the same build on 2026-08-04; nothing in the session's context pointed at the tool for it to be called wrongly.

What the run measured is the route, not the default. Of the nine published skills only typo3-core-patch-development and typo3-core-patch-review name typo3_commit_message_guide, and both are for core patches. The seven an extension author would reach for name no commit step at all, and none of them activated here either. typo3_task_guide, which does compute outsideCore and now names the commit guide in its follow-up calls, is only reachable by being called - and it was not. The same holds for the test half of the task: the fix carries no test, the checkout has no Tests/ and composer test is phplint alone, and typo3_test_run_guide was as unreached as the rest.

## Query

Somebody reported that the language caption never shows up on the code blocks on our website - the little "PHP" or "JavaScript" label Prism puts in the corner of a highlighted block. Their browser console says "Show Languages plugin loaded before Toolbar plugin." Reproduce it, fix it, and commit it - we tag a patch release afterwards.

## Suggestion

The commit step has to be reachable from where an extension author already is. Today it is named in two skills that both open with the core's Gerrit workflow, so a session fixing a bug in its own extension has no route to it and reaches the commit from its own habits - which happened to produce a conforming message here, and is not something this server can be credited with or rely on. Naming typo3_commit_message_guide with workflow="project" in the extension-facing skills, where the fix-and-commit task actually lands, is the cheaper half. The routing entry for "Writing or amending the commit message" in knowledge/server-scope.json still does not name the argument either, although the covered topic does.
