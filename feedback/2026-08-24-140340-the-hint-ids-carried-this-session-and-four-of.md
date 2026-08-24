---
date: 2026-08-24T14:03:40+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup
directory: /home/benji/projects/ext-usercentrics
---

# The hint ids carried this session and four of them must not be changed

## Observation

Task: one version of a TYPO3 extension serving v13.4 and v14, with unit tests, functional tests and a browser-verifiable installation.

What worked, concretely, so it is not broken later. All four were fetched by id, not by query.

project-configuration-files predicted a failure before I hit it: "the first ddev start in a clone whose dependencies are not installed leaves no additional.php, and every request then fails with exception 1396795884 ... That detection also runs before the post-start hooks". My first start on a clone-like state answered HTTP 500 on every URL with an empty log. I knew what it was in one step instead of hunting. The same hint also warned that "a regeneration can overwrite additional.php while restoring an /additional.php ignore entry" — DDEV wrote exactly that config/system/.gitignore two turns later and I recognised it instantly.

extension-test-site stopped me writing a test that could not run: "SiteBasedTestTrait is not available. The core's Tests/ directories are export-ignored". It handed me the replacement in one line, $this->get(SiteWriter::class)->write(), and the non-obvious part — "the file-backed caches live in that instance too ... CacheManager::flushCaches() after writing the configuration is what makes the class deterministic", with the sting that "the test passes when it is run alone with --filter". I put the flush in before running anything and the functional suite was green on its first execution, nine tests, no debugging.

project-extension-tests (which arrived inside typo3_task_guide, not from a direct call) named the five typo3Database* variables and that the account must create one database per test class, "under DDEV that means root rather than the user the site itself runs as". I wrote that into .ddev/config.yaml before the first functional test existed. It worked first try.

extension-repository-installation corrected an assumption before I acted on it: "extra.typo3/cms.app-dir moves nothing ... the application directory is the Composer root, which here is the repository itself — config/ ... and var/ ... are written into the versioned tree and belong in .gitignore". Seeing config/ and var/ appear in a distributed extension's repository root looked like a misconfiguration and I would have tried to move them. That is a dead end I did not enter.

extension-manifest (also via task_guide) told me TYPO3 v14 still evaluates ext_emconf.php unless composer.json declares both a version and providesPackages, and that "a functional test suite running with failOnDeprecation is usually the first thing to surface" it. I fixed it before writing the suite rather than after it went red.

## Query

typo3_hint_lookup id=project-configuration-files; id=extension-test-site; id=extension-repository-installation; id=installation-setup. Plus typo3_task_guide changeType=operations, which returned project-extension-tests, core-tests, site-sets, environment-placeholders and extension-manifest whole.

## Suggestion

Keep these ids stable and keep the predictive sentences in them — the value was never the API name, it was "this will look like X and the cause is Y". One thing that made them reachable at all: every hint answer lists availableHints and every task_guide answer lists omittedHints, so an id I could not have guessed from its name arrived as data. project-configuration-files is the case in point: I would have searched for "additional.php" or "trusted hosts", neither of which is in its title. Do not drop those two fields.
