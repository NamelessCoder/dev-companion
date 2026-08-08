---
date: 2026-08-08T22:44:55+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_rule_lookup, typo3_forge_lookup, typo3_hint_lookup
directory: /home/benji/projects/typo3-cms
---

# four answers that pre-empted a wrong path in one review, recorded so they are not refactored away

## Observation

Task: review Gerrit change 95179 ("[BUGFIX] Let stdWrap override apply the value 0") in a git worktree of a TYPO3 core checkout. Filed as the positive counterpart to my other feedback from this session: these are the answers that changed what I did, and breaking them later would cost more than any gap I reported.

1. typo3_test_run_guide, the biggest save. Its invocation.preconditions said that a suite runs against the vendor/ and bin/ of the directory it is started from, that a git worktree starts without both because /vendor/* and /bin/* are gitignored, and that the run then stops at "exec: line 9: bin/phpunit: not found" — a message that names phpunit rather than the missing directory. I had just created the worktree. I ran composerInstall first and never saw the error. Its cglGit note is the same class of save: "a git worktree keeps its gitdir outside the mounted directory: git fails, the list is empty, and the suite reports SUCCESS having read nothing". I ran cgl -n instead and got a real result over 6300 files. A green cglGit in a worktree is a false verification I would have reported as a checked surface.

2. typo3_rule_lookup, section "Changelog Files", corrected an assumption in both directions before I acted on it. I was ready to settle "BUGFIX owes no changelog" and move on. The section says that, and also says Important exists "for anything else that may require manual action" and is the only one of the four an LTS release may carry, and also says demanding an entry of a BUGFIX that removes nothing public is itself a review defect. That three-sided answer is why the finding was filed as "a reviewer would ask, here is the precedent, here is the rule that cuts the other way" instead of as a blocker or as nothing.

3. typo3_rule_lookup, section "Release Targets", stopped me reading `git branch -r` as the answer to which branches take a patch, and flagged that an out-of-support line is an error rather than a nicety. I then verified the defect on origin/14.3 and origin/13.4 and found origin/12.4 carries it too — and left 12.4 out of the report as correctly excluded ELTS instead of suggesting it be added.

4. typo3_forge_lookup(issue 81619) produced a finding the commit message alone could not. The 2017 report says the override is needed "if a result of TypoScript condition should be overriden". That sentence is what told me the patch's tests, which all call stdWrap_override() directly with a literal conf, never exercise the path the issue is about. I then probed that path and it worked, but nothing in the patch guards it.

Also worth keeping: the typo3_hint_lookup core-tests hint contains a paragraph on where output expectations hide (fixtures as .csv, .xml, .typoscript, heredoc PHP; values split across sprintf or escaped in PCRE). It aimed my search at the whole Tests/ tree rather than *Test.php. The search came back empty, but it came back empty for the right reason.

## Query

Whole session: review Gerrit change 95179 in a git worktree. Calls in order: typo3_project_describe, typo3_gerrit_lookup(change 95179), typo3_forge_lookup(issue 81619), typo3_hint_lookup(paths ContentObjectRenderer.php + ContentObjectRendererTest.php, targetVersion 15.0), typo3_rule_lookup(query "breaking change changelog entry bugfix behaviour change release branches"), typo3_commit_message_guide(workflow core), typo3_changelog_lookup(query "stdWrap override"), typo3_test_run_guide(paths, targetVersion 15.0). Eight MCP calls, no repeats with different arguments, none that only restated a previous answer.

## Suggestion

Nothing to change. Recorded so that a later refactor of typo3_test_run_guide's preconditions block, or a compression of typo3_rule_lookup's Changelog Files section down to its first sentence, is recognisable as a regression. If the preconditions text is ever shortened, the two worktree sentences and the literal "bin/phpunit: not found" string are the load-bearing parts.
