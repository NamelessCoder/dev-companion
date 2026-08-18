# say in the project answer which declared commands can start on the PHP that would run them

**Serves:** feedback/2026-08-18-113412-a-declared-quality-check-that-would-not-start.md, D-ANS-086
**Priority:** normal
**Branch:** todo/a-declared-quality-check-that-would-not-start
**Claimed:** 2026-08-18

Judged as the ladder's step 1b: `typo3_project_describe` marks `composer cgl:ci`
a `check` a review may run and carries nothing that says it aborts before the
fixer starts — `D-ANS-086`, which is also why `typo3_test_run_guide` is not
widened. Read the bound out of `<vendor>/composer/platform_check.php` the way
`Typo3Cli::installedPhpBound()` already does, moving that method somewhere
`Project` reaches rather than writing a second parser for the same file, and
report it as a fourth PHP number in `Project::describe()` and in
`ProjectDescribe::outputSchema()` — absent meaning no bound, which is what
Composer leaves behind where nothing requires a version. Then say where it
stands against `environment.php` in `phpRelation`, extending
`ProjectDescribe::relation()` and `whereTheyRun()` beside the sentence that
already names the container's interpreter. What is still open is the repository
that configures no environment: the commands run in the caller's shell, whose
PHP this server does not read, so decide whether the answer states the bound
alone there or discovers an interpreter, and write which into `D-ANS-086`.
`ProjectTest` is where the assertions go, beside the four `R-PRJ-007` names.
