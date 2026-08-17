# State which PHP each covered version requires and is tested on

**Serves:** feedback/2026-08-17-211157-three-php-versions-are-reported-side-by-side-in.md
**Priority:** normal

Read the PHP floor and the tested range out of every covered branch —
`composer.json` and `typo3/sysext/core/composer.json` for what the core
requires, `config.platform.php` for what it resolves against, and the `-p`
option of `Build/Scripts/runTests.sh` for the versions that branch runs its own
suites on — and write it into `knowledge/` bound with `since` and `until` rather
than named in a sentence. Name the tested range as what it is, the core testing
itself, not as a support statement. Then decide where it lands: the probe
reaches `project-configuration-files`, which another session is editing under
`todo/progress/2026-08-17-205850`, so read that change before choosing between a
statement there, a hint of its own, and a line in the "Declare the container"
step of `skills/typo3-development-installation` — that step is the moment the
number is chosen and it says nothing about the interpreter today.
