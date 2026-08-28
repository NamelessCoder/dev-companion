---
date: 2026-08-28T00:13:45+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide, typo3_test_run_guide
directory: /home/benji/projects/bootstrap_package
---

# task_guide routes an extension test task at typo3_test_run_guide, which is core-only by its own d...

## Observation

Task: add functional regression tests to a TYPO3 extension (bk2k/bootstrap-package, Tests/Functional/Parser/).

typo3_task_guide with changeType="test" and paths ["Tests/Functional/Parser/ScssParserTest.php", "Classes/Parser/AbstractParser.php"] got the classification right — it answered scopes [{scope: "extension"}, {scope: "extension"}] and scope: "extension" for the task as a whole. It then listed as the first entry of nextTools:

  {"tool": "typo3_test_run_guide", "when": "for the targeted invocation form"}

I fetched that tool's schema on the strength of the recommendation. Its own description then says it answers about Build/Scripts/runTests.sh and that "the script belongs to the core repository, so paths that read as a project or third-party extension get no suite at all rather than commands that cannot run there". The project-extension-tests hint, returned by the same workflow, says it flatly: "There is no runTests.sh outside the core."

So the answer routes an extension task at a tool documented to return nothing for it, using a classification the same answer had already computed correctly one field earlier. I did not make the call — the schema fetch was the whole cost — but a session that trusts nextTools makes it, gets an empty or apologetic answer, and learns to trust the array less.

Worth saying that the rest of that same answer's routing was good and I used it: it named the owning skill, and it named typo3_hint_lookup id=project-extension-tests, which is where the invocation form actually lives (vendor/bin/phpunit -c Build/<config>.xml). The wrong entry sat first in a list whose other entries worked.

## Query

typo3_task_guide(task="Add functional regression tests proving SCSS cache files are found when the current working directory is not the public path", paths=["Tests/Functional/Parser/ScssParserTest.php","Classes/Parser/AbstractParser.php"], changeType="test") — see nextTools[0], against scopes[] and scope in the same answer.

## Suggestion

Filter nextTools by the scope the same answer already computed. Where no path is scope "core", drop typo3_test_run_guide from the list rather than ranking it first.

"The targeted invocation form" for an extension has two better owners, both already reachable: typo3_project_describe's declared commands (here `composer test:php:functional`, narrowed with `--filter`), and the project-extension-tests hint, which carries `vendor/bin/phpunit -c Build/UnitTests.xml`. Pointing the `when` line at either of those would have been correct for this session.
