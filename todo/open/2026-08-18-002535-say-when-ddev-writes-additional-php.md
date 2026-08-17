# Say when DDEV writes additional.php, where that file is owned

**Serves:** feedback/2026-08-17-205850-ddev-writes-additional-php-only-once-it.md
**Priority:** normal

The judgement is `D-KNW-085`. Establish what guards DDEV v1.25.1's generation of `config/system/additional.php` —
the `typo3` provider in `pkg/ddevapp`, around the `createTypo3SettingsFile` and
`writeTypo3SettingsFile` that `D-KNW-049` was confirmed against — so that the
condition a clone with no installed dependencies fails is read rather than taken
from the report, then state it on `project-configuration-files` in
`knowledge/hints/project.json` beside the sections DDEV writes, correct the
`installation-operations` checklist item in `knowledge/task-intents.json` that
says today the file is rewritten on every start, and write the requirement and
the `HintsTest` case that hold both.
