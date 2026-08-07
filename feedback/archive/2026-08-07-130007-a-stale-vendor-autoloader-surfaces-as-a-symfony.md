---
date: 2026-08-07T13:00:07+00:00
category: missing-knowledge
status: closed
closed: 2026-08-07
model: claude-opus-5[1m]
tool: typo3_script_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# a stale vendor autoloader surfaces as a Symfony class-not-found and nothing connects the two

## Observation

Task: verify Forge 109572 and prove the patch with the full core functional suite on sqlite, because the change reached Backend::insertObject() which every Extbase extension in the core uses.

Two of 12477 tests errored: TYPO3\CMS\Core\Tests\Functional\DependencyInjection\AsTextExtractorAttributeTest, both with Symfony\Component\DependencyInjection\Exception\InvalidArgumentException, "Expected to find class TYPO3Tests\TestTextExtractor\TextExtraction\HighPriorityPlainTextExtractor in file ... while importing services from resource ../Classes/*, but it was not found!". The file exists and declares exactly that namespace and class.

The cause: composer.json line 344 declares the PSR-4 mapping for TYPO3Tests\TestTextExtractor\, and the generated vendor/composer/autoload_psr4.php does not contain it. The fixture arrived with commit a8f67c4ceec and the local vendor/ was never regenerated. Establishing that cost a 45-minute full-suite run to obtain the two test names, plus two targeted runs.

The typo3://guides/core/testing/scripts guide does state the governing rule — "a checkout that already has vendor/ needs it again only after composer.json or composer.lock changed" — but it attaches only the missing-vendor symptom, "exec: line 9: bin/phpunit: not found", and spells that one out precisely because it is not readable from the message. The stale-vendor symptom is equally unreadable and is not there.

## Query

CI=true ./Build/Scripts/runTests.sh -s functional -d sqlite (full suite, 12477 tests, 2 errors); then the same limited to typo3/sysext/core/Tests/Functional/DependencyInjection/AsTextExtractorAttributeTest.php; typo3_script_lookup(task: "git hooks, commit and coding guidelines check before committing", targetVersion: "15")

## Suggestion

Put the second symptom beside the first in the scripts guide: a vendor/ that exists but predates a composer.json change fails as a Symfony DependencyInjection InvalidArgumentException naming a class that is plainly present in its file — most visibly for the TYPO3Tests\* fixture-extension mappings — and the fix is runTests.sh -s composer -- dumpautoload, not composerInstall. The message points at the fixture rather than at the autoloader, which is the same reason the bin/phpunit case already earns its paragraph.
