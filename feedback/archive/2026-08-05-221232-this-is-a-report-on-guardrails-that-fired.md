---
date: 2026-08-05T22:12:32+00:00
category: idea
status: closed
closed: 2026-08-06
model: claude-opus-5[1m]
tool: typo3_configuration_lookup, typo3_project_describe, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# This is a report on guardrails that fired correctly and must not be broken.

## Observation

This is a report on guardrails that fired correctly and must not be broken.

Task: triage old Forge issues against a 15.0.0-dev core checkout, then carry one to a patch. Three answers stopped me from acting on something false.

1. typo3_configuration_lookup, asked for a formDataGroup while DDEV was stopped, refused rather than guessing, and the refusal was actionable: unsupported / cause "installation-not-answering" / reason "the DDEV project is paused — start it with ddev start in /home/benji/projects/typo3-cms to answer from the installation". It named the cause, the fix, the directory, and the settings it reads. I ran `ddev start` and moved on. A tool that had silently answered from shipped defaults would have given me a plausible answer to a question that needs the running installation, and I would not have known the difference. This shape — refuse, name what is missing, name the command — is the single best failure mode in the server and should be the template everywhere.

2. typo3_project_describe at session start reported environment via=ddev, php=8.5, entered=false. That one field is why I ran every suite through the project's runner instead of the host shell, and it paid off concretely at the end: the Gerrit pre-commit hook died with "Your Composer dependencies require a PHP version >= 8.5.0. You are running 8.4.24" and printed "There was a coding guideline problem in one or more of your php files". Because project_describe had already told me the host and the container are different interpreters, I read that as an environment artifact rather than as a real CGL failure — and the container run had already reported 0 of 6300 files needing fixes. Without that field I would have chased a CGL problem that did not exist.

3. typo3_test_run_guide carried the warning that a green over no files is not a green. I acted on it: I read "6300 files" from cgl, "Fixed 0 of 2 files" from cglGit, "203 tests, 634 assertions" from the functional run, and treated each as evidence only because it named a count. In the same session my first reproduction test ran green in a state where it was testing nothing until I fixed the fixture — so the warning is not theoretical.

Also worth naming: typo3_forge_lookup returning `total: 39` beside a 25-row page is what told me the page was not the set. I re-queried with limit=50. A tool that returned rows without the total would have had me triage a page as if it were the backlog.

## Query

typo3_configuration_lookup with path "SYS/formEngine/formDataGroup/tcaDatabaseRecord" while the DDEV project was stopped; typo3_project_describe at session start; typo3_test_run_guide with the changed backend Form paths.

## Suggestion

Nothing to fix here. What to protect: the structured unavailability answer from typo3_configuration_lookup (cause, reason, the exact command, the directory, the settings consulted) — copy that shape to every tool that reads the installation rather than the packages. The `entered:false` field in typo3_project_describe, which is doing more work than its size suggests. The count-bearing totals in typo3_forge_lookup. And the "a green over no files is not a green" line in typo3_test_run_guide, which changed how I read four separate check results in one session.
