---
date: 2026-07-29T10:06:19+00:00
category: idea
status: open
tool: typo3_catalog_scope
---

# The bundled catalogs come from main / TYPO3 15.0 (commit 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0...

## Observation

The bundled catalogs come from main / TYPO3 15.0 (commit 4c8b38b2dd07856c3e2666fbdfd77beead87ffe0, verified 2026-07-28), while the installation this server discovered and reads is TYPO3 v14. The server therefore already knows both numbers but never contrasts them, so version skew stays invisible unless the caller thinks to call typo3_catalog_scope. One concrete instance: typo3_test_run_guide uses typo3/sysext/core/Tests/Unit/Localization/TranslationDomainResolverTest.php as its worked example and typo3_server_scope credits the domain rules to TranslationDomainResolver, but v14 ships TYPO3\\CMS\\Core\\Localization\\TranslationDomainMapper and has no TranslationDomainResolver anywhere in vendor/typo3. Worth saying explicitly that the computed domains themselves are right: I verified typo3_translation_domain_lookup against v14 TranslationDomainMapper::transformFilePathToResource and the UpperCamelCase-to-snake_case rule, and Configuration/Sets/FranksBallerbude/labels.xlf -> franks_ballerbude.sets.franks_ballerbude and Resources/Private/Language/locallang.xlf -> franks_ballerbude.messages both match the running code exactly. Only the class name drifted.

## Query

{} -- compared against the v14.3 installation the server was started in

## Suggestion

Since the installation is already detected, read its version once and surface the delta where it matters: a one-line note in typo3_catalog_scope such as "catalogs are from 15.0; the installation here is 14.3 -- verify component and class names against it", and the same note attached to a component or class-name answer when the two majors differ. Also re-check the TranslationDomainResolver/TranslationDomainMapper naming, since one of the two is wrong for any given branch.
