# Put the two core-checkout symptoms in the scripts guide

**Serves:** feedback/2026-08-07-125950-the-git-pre-commit-hook-fails-on-host-php-and.md, feedback/2026-08-07-130007-a-stale-vendor-autoloader-surfaces-as-a-symfony.md
**Priority:** normal

Verify both against a core checkout, then write them into
`knowledge/documents/core/testing/scripts.md`, where the same shape already
earns a paragraph: the guide spells out `exec: line 9: bin/phpunit: not found`
precisely because that message does not read as a missing `vendor/`. Two more
that do not read as their cause. First, the repository's `.git/hooks/pre-commit`
runs on the host PHP rather than in the container, so on a checkout whose
declared PHP is newer than the host's it dies in
`vendor/composer/platform_check.php` and then prints a missing-or-wrong php file
header error for every commit regardless of content; the commit is created
anyway, the header error is false because the hook never read a file, and
`CI=true ./Build/Scripts/runTests.sh -s cglGit` is what settles it against the
commit. Second, a `vendor/` that exists but predates a `composer.json` change
fails as a Symfony `InvalidArgumentException` naming a class that is plainly
present in its file — most visibly for the `TYPO3Tests\*` fixture-extension
mappings — and the fix is `runTests.sh -s composer -- dumpautoload` rather than
`composerInstall`. Establishing the second cost a 45-minute full-suite run, so
it is worth the paragraph. Check the guide's existing rule while there: it says
a checkout with `vendor/` needs it again only after `composer.json` or
`composer.lock` changed, which is the rule this symptom is the unreadable face
of.
