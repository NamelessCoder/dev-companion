---
date: 2026-08-07T13:00:58+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_script_lookup, typo3_test_run_guide, typo3_server_scope
directory: /home/benji/projects/typo3-cms
---

# script_lookup carries the scripts guide but test_run_guide answered first so it stayed unused

## Observation

Task: verify Forge 109572 and run the core checks for the patch. This corrects and sharpens a feedback I filed earlier in the same session.

That earlier one said the typo3://guides resources were never discovered because my client showed no resource list and I never called typo3_server_scope. Calling typo3_script_lookup now, during the debrief, shows the picture is different: it returns a guide directly and inline — typo3://guides/core/testing/scripts, "TYPO3 Core Script Help" — as an ordinary match with its body. So the guides were reachable through a normal lookup the entire session, and I still never saw one while doing the work.

The reason is overlap, not obscurity. typo3_test_run_guide answered every runTests.sh question I had, early and thoroughly: the suites, the targeted invocations, the -d sqlite|mariadb|postgres option, the -b docker|podman option, the CI=true prefix. I had no open question left to take to typo3_script_lookup, whose own description — "notes for TYPO3 core scripts and commands" — reads as a subset of what I had already been given. The guide turns out to carry material test_run_guide does not: the composerInstall precondition and why symlinking vendor/ does not substitute, and the git-worktree caveat that makes cglGit report SUCCESS having read no file.

So the finding is not that the guides are unreachable. It is that the tool a session actually reaches for is the one that keeps the fuller document out of sight.

## Query

typo3_test_run_guide(query: "functional", paths: ["typo3/sysext/extbase/Classes/Persistence/Generic/Storage/Typo3DbQueryParser.php", "typo3/sysext/extbase/Tests/Functional/Persistence/OperatorTest.php"], targetVersion: "15") early in the session; typo3_script_lookup(task: "git hooks, commit and coding guidelines check before committing", targetVersion: "15") only in the debrief, which returned typo3://guides/core/testing/scripts with coverage 0.5 and truncated: true

## Suggestion

Have typo3_test_run_guide name the typo3://guides/core/testing/scripts resource in its answer, the way typo3_task_guide names the skill that owns a task. That is the one moment a session is certain to be looking, because it is about to run something. Read this together with my earlier feedback on server_scope: the fix is not to make sessions ask what exists, it is to have the tool they do call hand the guide over.
