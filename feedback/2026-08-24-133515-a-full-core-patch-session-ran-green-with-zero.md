---
date: 2026-08-24T13:35:15+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_task_guide, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# A full core patch session ran green with zero calls to this server and no skill opened

## Observation

Task: write a TYPO3 Core patch with functional tests for a stale site-settings/TypoScript-cache bug described in a handover artifact, then draft the Forge issue and the commit message.

The session produced a complete, green core patch — two Classes changed, one test added to an existing functional test case, one new functional test case, unit + functional + cgl + phpstan all run — and it made zero calls to this server and activated zero skills. That is the finding. Everything the server exists for was done by grep/sed over the checkout and from AGENTS.md.

Where the decision was made: the very first turn. The request arrived in German, as a one-liner plus an artifact URL, with no TYPO3 vocabulary in it at all — no "core", no "patch against main", no class name. The MCP server's own initialize instructions say "Start every task with typo3_project_describe", and I did not, because at the moment of reading that request the task looked like "read a URL". By the time the task had revealed itself as core-patch work (three files open: SysTemplateTreeBuilder.php, SiteSettingsService.php, SysTemplateTreeBuilderTest.php) I was already inside a working loop and never re-evaluated. Nothing in the loop prompts a re-check.

typo3-core-patch-development matches this task exactly by its description ("Write a TYPO3 core patch and carry it to review: the changelog entry, the project's checks, the push to Gerrit. Also amending after review and backporting to a release branch."). It stayed shut. Two plausible causes, and I cannot tell them apart from inside: (a) the trigger text was German and every skill description is English — the lexical-matching warning the server gives for its own tools plausibly applies to skill selection too; (b) the first turn's surface task was "read an artifact", and skill selection happens on that turn.

The concrete costs of running without it: I decided on my own that a BUGFIX needs no RST changelog entry (correct, from AGENTS.md); I worked out the Releases: line from Typo3Version::BRANCH by grep; I guessed at typo3/sysext/tstemplate/Tests/Functional existing and burned one full container test run on "Test file not found"; and I emitted a Signed-off-by: trailer twice that the user had to strike, although my own project memory already recorded that they do not use it.

Nothing here is a wrong answer by the server. It is a session where a well-matched skill and a prescribed opening call both went unused, and the transcript shows exactly which turn decided that.

## Query

Task text, verbatim and in German: "bitte schaue dir das hier an und baue eine patch mit tests dafür https://claude.ai/code/artifact/<id>" — followed later by "ich brauche einen titel für das forge issue und eine beschreibung" and "hier ist das forge issue 110527". Working directory was a TYPO3 Core monorepo checkout on branch main (Typo3Version::BRANCH = 15.0). The artifact was a prior session's handover document analysing a stale typoscript-cache bug.

## Suggestion

A re-entry point for a task that reveals itself late. The prescribed "start every task with typo3_project_describe" only works if the first turn already looks like TYPO3 work. A session that starts with "look at this URL" and lands in typo3/sysext/core/Classes/ ten calls later has no prompt to go back. Something that fires on the checkout — first edit under typo3/sysext/*/Classes/, or first invocation of Build/Scripts/runTests.sh — would have caught this one, and would have caught it before the wasted test run and the wrong commit footer rather than after.
