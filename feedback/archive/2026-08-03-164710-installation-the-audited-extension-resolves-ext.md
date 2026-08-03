---
date: 2026-08-03T16:47:10+00:00
category: missing-knowledge
status: closed
closed: 2026-08-03
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation.

The audited extension resolves EXT: image paths in Classes/Utility/MascotResolver.php with:

    PathUtility::getSystemResourceUri($extPath, $request)

That method exists and is current in the installed core: cms-core/Classes/Utility/PathUtility.php:135, signature (string $resourceIdentifier, ?ServerRequestInterface $request = null, ?UriGenerationOptions $options = null): UriInterface.

The `public-assets` hint presents exactly one route for this in v14: "In PHP the URL comes from the System Resource API: inject SystemResourceFactory, call createPublicResource() with the identifier ... and hand the result to SystemResourcePublisherInterface::generateUri()". It also names PathUtility::getPublicResourceWebPath() as the pre-v14 way, which the deprecation sweep independently confirmed as deprecated (#107537).

The static convenience method the code actually uses is named nowhere in the hint. Read literally — and the conformance skill instructs rules to be read literally in both directions — the hint says the code under review is not using the supported API, on an API surface where the neighbouring method genuinely is deprecated. That is a false positive waiting to happen: correct, current code reported as a finding against a maintainer. I only avoided it by grepping PathUtility.php for both method names, which is a roundtrip the hint caused rather than saved.

This is the failure mode the skill's finding gate is meant to catch, but it catches it only if the reviewer distrusts the hint enough to read the class — and a hint that is confident and incomplete is precisely the one that does not invite that.

## Query

typo3_hint_lookup {task: "Backend JavaScript ES modules, import maps and public assets shipped by an extension for the TYPO3 backend", paths: ["Configuration/JavaScriptModules.php", "Resources/Public/JavaScript/", "Resources/Public/Css/"], targetVersion: "14.3"} → hint id `public-assets`

## Suggestion

Name PathUtility::getSystemResourceUri() in the `public-assets` hint as the static shorthand for the same resolution, with the relationship between the two entry points spelled out: the factory/publisher route is what a replaceable publisher or a CDN needs, the static method is the short path for a fixed EXT: identifier, and getPublicResourceWebPath() is the deprecated predecessor of both. Where an API has two supported entry points, a hint that lists one turns correct code into a reviewable finding — so the general fix is to state supported alternatives explicitly rather than only the recommended one.
