---
date: 2026-08-18T07:03:58+00:00
category: wrong-answer
status: open
model: claude-opus-5[1m]
tool: typo3_task_guide
directory: /home/benji/projects/blog
---

# task_guide scoped every path "uncertain" in an extension repository and recommended core-only run...

## Observation

Task: boot the local DDEV development installation of the t3g/blog extension repository (github.com/TYPO3GmbH/blog), a distributed third-party extension, not a core checkout.

Two things came back wrong in one answer.

1. Scope detection failed on all three paths: "scopes":[{".ddev/config.yaml":"uncertain"},{"composer.json":"uncertain"},{"blog":"uncertain"}], and "scope":"uncertain". I passed "blog" precisely because the parameter documentation says "An extension key counts as a path" — it is the extension key of the repository, declared at extra.typo3/cms.extension-key in the composer.json I passed alongside it. It still came back uncertain. So even the documented affordance for establishing extension scope did not establish it.

2. Downstream of that uncertainty, the answer recommended core-repository checks into a repository that has none:
   "checks":["CI=true ./Build/Scripts/runTests.sh -s unit","CI=true ./Build/Scripts/runTests.sh -s functional"]
   plus four testSuites entries (composerInstall, checkComposer, e2e-prepare, checkExtensionScannerRst) all built on ./Build/Scripts/runTests.sh, and a nextTools pointer to typo3_test_run_guide "for the targeted runTests.sh invocation — the suites it lists are the testSuites above".

There is no Build/Scripts/ in this repository. Build/ holds exactly FunctionalTests.xml, UnitTests.xml, phpstan.neon, phpstan-baseline.neon, phpstan-baseline-v13.neon, phpstan-baseline-v14.neon (verified with ls Build). The repository's actual declared commands are composer test:php:unit, test:php:functional, phpstan, cgl, test — the same list typo3_project_describe returned correctly once an installation existed. checkExtensionScannerRst and checkComposer are meaningless outside the core monorepo; composerInstall names a script that is not there.

This directly contradicts the tool's own description: "Work that reads as a project or third-party extension is answered with what transfers only — the core checks, checklist items and steps that name something only the core repository has are left out rather than handed over." The suppression did not fire, and the reason appears to be that scope came back "uncertain" rather than "extension", so the filter had nothing to filter on.

It cost me nothing this session because an operations task does not run test suites — I never invoked them. A session doing testing or review work in this repository would have taken the runTests.sh line at face value, and its first act would have been a command that cannot exist. Worth noting the checkoutDiscovery block had the same tilt: it speaks of "the core checkout", "Core tests mirror the class path below typo3/sysext/<ext>/Tests/", and pushing to Gerrit — none of which applies to a GitHub-hosted extension.

By contrast the checklist and hints in the same answer were scoped correctly and were excellent (filed separately as what must not be broken). The defect is narrow: scope resolution, and the core-check suppression that depends on it.

## Query

typo3_task_guide task="Boot the local DDEV development installation for the blog extension repository: composer install, TYPO3 setup, site and demo content, so the backend and frontend answer", changeType="operations", paths=[".ddev/config.yaml","composer.json","blog"]

## Suggestion

Two fixes. First, make an extension key passed as a path resolve to scope "extension" — or, better, resolve scope from the repository the server was started in, which it already knows: root kind "composer-project", a root composer.json of type typo3-cms-extension with extra.typo3/cms.extension-key, and no typo3/sysext/ directory, is unambiguously not a core checkout, and that is decidable without any path argument at all. Second, make the core-check suppression fail closed: where scope is "uncertain", withhold the runTests.sh checks and testSuites rather than emitting them, or emit them labelled as core-repository-only the way withheldCategories already labels withheld hints elsewhere. Where the repository declares its own commands, those are the checks to hand back — typo3_project_describe already reports them with check/change/unknown markings, and task_guide could carry the same list.
