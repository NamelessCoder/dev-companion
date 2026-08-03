---
date: 2026-08-03T16:48:05+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_documentation_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation.

There is no cheap way to ask "is this API still current in version N", and that question came up repeatedly. The changelog answers "what changed", which is a different question, and the base of the conformance skill states the asymmetry correctly: a pattern nothing has touched for ten majors has no entry at all, so an empty sweep is not an answer about what still works. It then routes that question to typo3_documentation_lookup — which, for the queries I ran, did not reach the relevant pages (filed separately). So the routing exists but terminates nowhere, and the fallback is reading installed source.

Concrete instances from this one audit:

- The code calls PageRenderer::addInlineLanguageLabelFile(). The v14 deprecation sweep returned addInlineLanguageDomain() (#108963), a neighbouring method with a similar name and a real deprecation. Establishing that the method actually used is *not* deprecated, and still populates TYPO3.lang, took two greps of PageRenderer.php (the method at :821, and :1304/:1355 where inlineLanguageLabelFiles is rendered into the page).
- PathUtility::getSystemResourceUri() — current, but adjacent to a deprecated sibling; grep of PathUtility.php (filed separately as a hint gap).
- InfoboxViewHelper::STATE_ERROR — turned out to be deprecated, but only a docblock `@deprecated`, not a PHP #[\Deprecated] attribute, which decides whether it raises E_USER_DEPRECATED at runtime today or merely breaks at v15. That distinction changes the severity of the finding and is not derivable from the changelog entry.

In each case the question is narrow and the answer is a fact about the installed packages: does this identifier exist in the installed version, is it marked deprecated, is the marking a docblock or an attribute, and what does the entry say about removal. That is a mechanical read of code this server already has on disk.

## Query

typo3_changelog_lookup {type: "deprecation", version: "14", tag: "ext:backend"} returned Deprecation-108963 addInlineLanguageDomain; the audited code calls PageRenderer::addInlineLanguageLabelFile(), for which no entry exists — settled instead by grepping cms-core/Classes/Page/PageRenderer.php

## Suggestion

A lookup that takes a class, method, constant or property identifier and answers, from the installed packages: whether it exists in this version, its signature, whether it carries an @deprecated docblock or a #[\Deprecated] attribute (they differ in whether anything is raised at runtime today, which changes a finding's severity), the changelog entry if one exists, and the replacement the entry or docblock names. It closes the "does this still work here" loop the base opens and cannot currently finish, and it would have replaced five separate greps into vendor source in this audit alone. The distinction between a docblock and an attribute is the part no other source gives you.
