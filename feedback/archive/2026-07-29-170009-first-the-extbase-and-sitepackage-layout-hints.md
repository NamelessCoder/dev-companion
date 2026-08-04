---
date: 2026-07-29T17:00:09+00:00
category: missing-knowledge
status: closed
closed: 2026-07-29
commit: 04a5265
subject: "[FEATURE] Cover the test suite a project extension has to build first"
tool: typo3_task_guide, typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# First: the extbase and sitepackage-layout hints are now in the catalog, and they are accurate —...

## Observation

First: the extbase and sitepackage-layout hints are now in the catalog, and they are accurate — the Extbase one answered questions in this session that I previously had to break the site to learn. Thank you. This note is about the next layer.

WHAT THE TESTING ANSWER GIVES AND WHAT IT COSTS TO GET FROM THERE TO A GREEN SUITE.

The "core-tests" hint is correct and useful: UnitTestCase / FunctionalTestCase, final classes, #[Test], $coreExtensionsToLoad / $testExtensionsToLoad, CSV fixtures, $this->get() for services, tests mirroring the class path. All of it transferred to a project extension unchanged. But it describes how a test is written inside the mono repository, where the harness already exists. In a project there is no harness, and everything between "composer require" and the first green run is absent:

1. The suite has to be assembled. typo3/testing-framework ships UnitTests.xml, FunctionalTests.xml and the two bootstraps under Resources/Core/Build/ and says in a comment that extensions should copy rather than reference them. Nothing in the knowledge base says that, so the file is found by looking for it.

2. Functional tests need database credentials in the environment (typo3DatabaseDriver, typo3DatabaseHost, typo3DatabaseName, typo3DatabaseUsername, typo3DatabasePassword), and the user needs CREATE DATABASE because every test class gets its own. Under DDEV that means root rather than the account the site runs on. Without them the failure is "Database credentials for tests are neither set through environment variables, and can not be found in an existing LocalConfiguration file", which does not name the variables.

3. $testExtensionsToLoad takes paths relative to the DOCUMENT ROOT, not the project root. In a Composer project with web-dir public/, "extensions/my_sitepackage" resolves to public/extensions/my_sitepackage and fails with "Test extension path ... not found". The extension key alone works, because getPackageInfoWithFallback() resolves it — that is the form a project wants and it is not documented anywhere I could find.

4. The extension's ext_emconf.php dependency list has to be mirrored in $coreExtensionsToLoad, or the instance dies with "Package X depends on package Y which does not exist".

5. SiteBasedTestTrait — the trait every core frontend test uses, including theme_camino's — is NOT shipped: typo3/cms-core excludes Tests/ from the Composer package. A project extension has to write the site configuration itself. SiteWriter from the container does the whole job in fifteen lines, but only if you know to look inside the trait to find that out.

6. The one that cost the most: setUpFrontendRootPage() and site sets are incompatible. It writes a sys_template with clear = 3 hardcoded, SysTemplateTreeBuilder honours that flag by resetting the AST, and the site set TypoScript was added before it — so the page dies with "No page configured for type=0" and nothing points at the sys_template as the cause. A test that relies on a set must not call it. Plugin settings belong in the pi_flexform of the fixture content element instead, which is also more faithful.

7. InternalRequest::withQueryParameters() accepts scalars only. An Extbase plugin argument is nested two levels deep, so a search request has to be built into the URL with http_build_query().

Two more findings from the run itself, which are arguments for the suite existing at all: failOnDeprecation surfaced that ext_emconf.php is deprecated on 14.3 unless composer.json declares extra.typo3/cms.version and Package.providesPackages (#108345) — a project sitepackage should simply delete the file. And the FULLTEXT index in ext_tables.sql is created by the testing framework, so a repository test covers that file as a side effect.

## Query

typo3_task_guide task="Add automated tests for a project sitepackage extension: unit and functional tests for an Extbase model, repository and controller, plus frontend tests for the rendered pages", changeType=test, targetVersion=14.3 → hint "core-tests" plus the "Testing Strategy" rule

## Suggestion

Add a hint next to core-tests — "Testing a project extension" — carrying: copy the two phpunit XMLs and bootstraps out of typo3/testing-framework rather than referencing them; t
