---
date: 2026-08-19T09:42:48+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_rule_lookup
directory: /home/benji/projects/blog
---

# typo3_test_run_guide stayed shut through eight round trips of working out how to run the suite

## Observation

Task: full audit of the blog extension before its v14 release, which included running the test suites the repository declares.

typo3_test_run_guide was in my deferred-tool list from the first message of the session. I read that list, passed over it, and never called it once — while spending roughly eight round trips working out by hand exactly what its name promises. In order:

1. composer test:php:functional on the host -> 114 tests, 114 errors, "Database credentials for tests are neither set through environment variables, and can not be found in an existing LocalConfiguration file". I nearly reported this as a repository defect before diagnosing it as environmental.
2. Single test with --filter to read the real exception.
3. ddev describe -> containers exited.
4. ddev start.
5. ddev exec with typo3DatabaseUsername=db -> "Access denied for user 'db'@'%' to database 'db_ft6bb8ed7'". The db user cannot create the per-run test databases.
6. Retry with root/root -> works.
7. Later, from inside a git worktree: ddev exec refuses entirely — "a project (web container) in running state already exists for blog that was created at /home/benji/projects/blog". Had to switch to docker exec against ddev-blog-web with -w.
8. Later again, on a v13 install: "This version of PHPUnit requires PHP >= 8.3. You are using PHP 8.2.30" — the v13 dependency resolution pulls PHPUnit 12, the DDEV container runs PHP 8.2. Had to run the suite from the host against the port-mapped DDEV database (127.0.0.1:32840) instead.

Every one of those is a fact about running a TYPO3 extension's functional suite, not about this repository. Four of them (root rather than the project db user; ddev exec and worktrees; the container PHP against what the lockfile resolves; the port mapping as the way out) are the kind of thing a guide exists for.

Why I passed it over: the name reads as if it belongs to the core. This server's other test-shaped surface is core/testing/scripts, "TYPO3 Core Script Help", about runTests.sh. I filed typo3_test_run_guide next to it in my head and never checked. The one-line description in the deferred-tool listing is all I saw, and a tool is chosen on its description alone.

I did read extension/testing/phpunit later, and it is the right page — it has a "Database credentials for the functional suite" section. I only found it because typo3_rule_lookup returned the full document list as a side effect of a call that matched nothing. Nothing in the session routed me to it: typo3_task_guide with changeType=audit did not name it, and the extension-health skill does not either.

## Query

Never called: typo3_test_run_guide. The task text that should have reached it: "run the test suites this extension declares" / "composer test:php:functional fails with 114 errors" / "run the functional suite against a second declared TYPO3 major inside a git worktree".

## Suggestion

Route to the answer. typo3_task_guide with changeType=audit returned a brief whose checklist says "Run the checks this repository declares rather than recommending them" — that is exactly the moment the answer is needed, and the brief names none.

The content I would have wanted, in one page: which database user the functional suite needs and why the project user is not enough; that ddev exec is bound to the project directory and what to use from a worktree; that the PHP in the declared container and the PHP the lockfile resolves for PHPUnit can disagree, with the port-mapped database as the way out; and that missing credentials produce an error that reads like a broken test suite.

## Answered

The half about typo3_test_run_guide. Its suites are Build/Scripts/runTests.sh
invocations and that script is in the core repository, so a project or extension
path already gets a decline rather than commands that cannot run there. It was
passed over correctly, and neither renaming it nor adding it to the audit brief
is the lever — `D-ANS-086` and `D-ANS-092`.

Two of the four facts asked for are already here, in the
`project-extension-tests` hint and in the extension PHPUnit document beside it:
the account has to be allowed to create a database per test class, and the
message a missing credential stops with does not name what it is missing. What
is open is reaching them, and the two DDEV facts beside them.
