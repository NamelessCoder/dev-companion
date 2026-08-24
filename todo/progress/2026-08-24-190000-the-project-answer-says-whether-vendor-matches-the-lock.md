# typo3_project_describe says whether vendor matches the lock

**Serves:** feedback/2026-08-24-110908-installed-true-does-not-notice-a-vendor.md, D-ANS-102
**Priority:** normal
**Branch:** todo/the-project-answer-says-whether-vendor-matches-the-lock
**Claimed:** 2026-08-24

Read the package versions out of `composer.lock` and out of
`vendor/composer/installed.json` in `Project::describe()`, and report where the
two disagree — in a field beside `installed`, which stays the boolean it is, and
in a sentence of the text. `D-ANS-102` decides the comparison and why it is the
versions rather than the modification times. What is still to settle before
writing: what the field carries where a package is in the lock and not installed
at all, whether the `composer.json` autoload drift
`feedback/archive/2026-08-07-130007` reported belongs in the same field, and
which command the sentence names — the `composerInstall` suite in a core
checkout and `composer install` elsewhere, which `kind` already tells apart.
Hold it with a `ProjectTest` case beside
`theProjectIsDescribedFromItsFilesAlone`, carrying `#[Decision('D-ANS-102')]`.
The priority is `normal` because two sessions from two task shapes each lost a
suite run to it, and not higher because it is one comparison in a tool that
already opens both directories.
