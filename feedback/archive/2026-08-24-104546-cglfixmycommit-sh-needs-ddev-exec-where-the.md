---
date: 2026-08-24T10:45:46+00:00
category: missing-knowledge
status: closed
closed: 2026-08-24
model: claude-opus-5[1m]
tool: typo3_project_describe, typo3_test_run_guide, typo3-core-patch-review, typo3-core-patch-development
directory: /home/benji/projects/typo3-cms
---

# cglFixMyCommit.sh needs "ddev exec" where the core checkout declares a DDEV environment

## Observation

Task: review a local core patch in EXT:impexp before pushing it to Gerrit, in a core checkout that declares a DDEV environment.

typo3_project_describe reported the environment correctly (via: ddev, php 8.5, source .ddev/config.yaml, entered: false), and typo3_test_run_guide returned the runTests.sh suites. Neither says anything about Build/Scripts/cglFixMyCommit.sh, which is the post-commit CGL fixer the core's own contribution guidance tells an author to run, and which is not a runTests.sh suite.

The distinction the answers do not draw: the runTests.sh suites start their own container, so `./Build/Scripts/runTests.sh -s cgl -n` and `-s cglGit` run correctly from the host shell whatever the repository declares. cglFixMyCommit.sh does not — it runs php-cs-fixer with the caller's own PHP. In a checkout whose environment is DDEV, the interpreter that has to run it is the container's, so the invocation is `ddev exec ./Build/Scripts/cglFixMyCommit.sh` and not the `PHP_CS_FIXER_IGNORE_ENV=1 ./Build/Scripts/cglFixMyCommit.sh` form that the core repository's own AGENTS.md spells out. The user corrected me on exactly this after I had recommended the AGENTS.md form.

The same reasoning presumably reaches every other Build/Scripts/*.sh helper that is not runTests.sh, but I verified only this one, and only by being corrected — I did not run either form.

## Query

typo3_project_describe (no arguments, core checkout at a repository declaring .ddev/config.yaml); typo3_test_run_guide with paths ["typo3/sysext/impexp/Classes/Import.php", "typo3/sysext/impexp/Tests/Functional/Import/MultilingualPagesAndTtContentTest.php", "typo3/sysext/impexp/Tests/Functional/Fixtures/DatabaseAssertions/importMultilingualPagesAndTtContent.csv"], targetVersion "15.0"

## Suggestion

Where an environment is declared and not entered, say which commands run through it and which do not, rather than only naming the environment. Concretely: typo3_project_describe already knows `via: ddev` and `entered: false`, so a scripted command it lists could carry the prefix that actually runs it — and typo3_test_run_guide could add one line to its invocation notes: the runTests.sh suites bring their own container and are run from the host as printed, while Build/Scripts/cglFixMyCommit.sh runs under the caller's PHP and therefore needs `ddev exec ./Build/Scripts/cglFixMyCommit.sh` in a DDEV checkout. The commit/patch workflows that tell an author to run the fixer after committing (typo3-core-patch-development, typo3-core-patch-review) are where the wrong form gets recommended, so the qualification is worth carrying there too.
