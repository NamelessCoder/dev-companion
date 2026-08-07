---
date: 2026-08-07T13:00:37+00:00
category: idea
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3-core-issue-triage, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# seen-it-fail does not survive the commit and git stash then proves nothing

## Observation

Task: verify Forge 109572, and then establish that two remaining errors in the full core functional suite were not caused by my patch.

typo3-core-issue-triage's rule that a reproduction has to be seen failing before it is believed is the single instruction that most improved this session. I applied it throughout: every assertion started with an impossible sentinel value, which is how I got the real numbers instead of my expectations, and how I later answered the user's challenge about whether the defect was postgres-specific by stashing the uncommitted fix and measuring it red on all three databases.

But the rule assumes the change is in the working tree. Once the patch was committed I tried the same move — `git stash push -- typo3/sysext/extbase/` — on a clean tree. It found nothing to save, I ran the tests, and I reported the result to the user under the heading "OHNE PATCH". It was not: the patch was in the commit. The `git stash list` output showed only a pre-existing unrelated stash, which is what should have stopped me. I noticed afterwards and corrected it, and the question was settled by independent evidence (a missing autoloader entry), but for one exchange I presented a measurement that measured nothing.

This is the same failure the skill already warns about for a suite reporting success having read no file, wearing a second costume: an operation that silently did nothing, followed by a result that looks like evidence.

## Query

git stash push -- typo3/sysext/extbase/ on a clean working tree with the change committed at fda437f2449, followed by CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite -- typo3/sysext/core/Tests/Functional/DependencyInjection/AsTextExtractorAttributeTest.php, reported as a without-patch measurement

## Suggestion

Put the committed case beside the green-over-no-files warning in typo3-core-issue-triage: once the change is committed, git stash is a no-op and proves nothing. Measure against the parent instead — a git worktree on HEAD~1, or git revert --no-commit with a restore afterwards — and confirm the tree actually changed before believing the run. The rule is right; it needs its second half for the point where a triage has become a patch, which is exactly where the skill hands over.
