---
date: 2026-08-05T03:39:54+00:00
category: idea
status: closed
closed: 2026-08-05
model: claude-opus-5[1m]
tool: typo3_test_run_guide, typo3_rule_lookup, typo3_project_describe, typo3_commit_message_guide, typo3_changelog_lookup, typo3-core-patch-development, typo3-core-issue-triage
directory: /home/benji/projects/typo3-cms
---

# This is the "what worked and must not be broken" half of the report. Task: triage old open RTE is...

## Observation

This is the "what worked and must not be broken" half of the report. Task: triage old open RTE issues, then write a core bugfix patch for #88556 against main (15.0.0-dev). The patch was delivered with no rework and no wrong turn, and specific answers from this server are why.

typo3_project_describe, one call, first thing: typo3Version 15.0.0-dev, corePhpConstraint ^8.5, environment via ddev at php 8.5, and the honest "extensions": [] for a bare core checkout. The PHP floor was the load-bearing fact — the host runs PHP 8.4, so every direct vendor/phpunit invocation dies on Composer's platform check, and the whole verification had to go through Build/Scripts/runTests.sh in a container. The server had told me that before I opened a file. (I ran host phpunit once anyway and hit the platform check; that was my error against information the server had already given me.)

typo3_test_run_guide with the two concrete changed paths was the single most useful call of the session. It returned the exact targeted invocations, which suites can fail on those paths, and three things I would not have known and would have got wrong:
  - the CI=true prefix and why it matters for a non-interactive agent
  - that podman is the default and -b docker is the switch, which is what the checkout actually needed
  - "A green that ran over no files is not a green" plus the warning that cglGit reads no files from a git worktree and still reports SUCCESS
I acted on the third: cgl reported "Found 0 of 6294 files that can be fixed", and I quoted the file count as the evidence rather than the word SUCCESS.

typo3_hint_lookup on the same paths returned the core-tests hint, which correctly told me a unit test is the right layer here, that tests mirror the class path, and — the part that saved real work — that expectations for changed output hide in fixtures rather than in *Test.php. I searched for other consumers of RteHtmlParser on that basis and found the functional tests in rte_ckeditor and core/Tests/Functional/Html/ that I then ran.

typo3_rule_lookup answered in one call the question that would otherwise have cost an unnecessary .rst file and a checkRst run: "A casual bug fix owes no entry, because its commit message carries the information." It also correctly named validateRstFiles.php and the Howto.rst as the authorities, which I did not need but would have.

typo3_changelog_lookup {query: "RteHtmlParser"} was exactly right and cheap: five entries, newest 9.5, oldest 8.0, all deprecations and removals. That established the area had not been reworked since the 2019 report, which is a required step of the triage and would otherwise have been a guess.

typo3_commit_message_guide with workflow="core" caught a 60-character subject against the preferred 52 and wrapped the body correctly at 72; the reworded second call came back clean. Two round trips, both earned.

Both skills fitted their tasks and both changed what I did rather than just describing it. typo3-core-issue-triage's checklist.md is the part I would keep hardest: its insistence that "not reproducible as written" and "gone" are opposite outcomes is exactly the distinction this issue turned on — the 2019 report still happens, the 2023 comment filed under the same number does not reproduce at that layer, and the checklist is why I filed two verdicts instead of one wrong one. Its instruction to separate what the reporter saw from what they believed caused it also paid off directly, since here the reporter's guessed cause ("DB=>CKEditor parse-process") happened to be right and I could say so with evidence. typo3-core-patch-development's "write the test first and prove it fails before the fix, in that order" is what produced the four red data sets I could show before touching the class, and its warning that a diff fixing two things is one a reviewer must accept or reject as one is why I did not also touch the related unclosed-tag behaviour I noticed.

## Query

Whole session: triage Forge #88556 against a core checkout, then write the patch. Calls in order: typo3_project_describe; typo3_forge_lookup (query x2, then issue x2); typo3_gerrit_lookup x7; typo3_changelog_lookup {query: "RteHtmlParser"}; typo3_rule_lookup {query: "bugfix changelog entry requirements and target branch"}; typo3_test_run_guide {paths: [RteHtmlParser.php, RteHtmlParserTest.php]}; typo3_hint_lookup {same paths}; typo3_commit_message_guide {workflow: "core"} x2.

## Suggestion

Keep all of the above. In particular keep typo3_test_run_guide accepting concrete changed paths and returning the invocation notes alongside the suites — the container runtime flag, the CI=true prefix and the "a green over no files is not a green" warning are the parts that make its output usable by an agent rather than merely correct. Keep the checklist of verdicts in typo3-core-issue-triage as a separate file the skill tells you to read before writing the answer; being made to pick the verdict first is what surfaced that one issue number held two different defects.
