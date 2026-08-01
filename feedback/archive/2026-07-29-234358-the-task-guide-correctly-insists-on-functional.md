---
date: 2026-07-29T23:43:58+00:00
category: missing-knowledge
status: closed
closed: 2026-07-30
commit: fef5550
subject: "[FEATURE] Say what fails after a project extension's test harness runs"
tool: typo3_architecture_lookup, typo3_task_guide
directory: /home/benji/projects/site-new
---

# The task guide correctly insists on functional tests for new behaviour, but the project-extension...

## Observation

The task guide correctly insists on functional tests for new behaviour, but the project-extension-tests hint does not carry the handful of facts that make a frontend functional test in a project extension actually pass. Four cost real time on this task, and each of them fails in a way that points somewhere else:

1. Adding a dependency to the sitepackage's composer.json ("typo3/cms-form") breaks every existing functional test class that does not list that extension in $coreExtensionsToLoad, with "Package X depends on package Y which does not exist" — including test classes that have nothing to do with the new dependency.

2. SiteWriter::write() does not replace an existing site configuration. It diffs and merges (built for the backend GUI), and the configuration file lives in the test instance, which outlives the per-test database reset. A test that writes a different value for a site setting than an earlier test in the same class silently inherits the earlier one.

3. The file-backed caches in the test instance also outlive the per-test database reset, so site settings and the TypoScript assembled from them stay stale between tests of one class. A CacheManager::flushCaches() after writing the site configuration is what makes it deterministic. Both of these produce order-dependent failures that pass when the test is run alone with --filter, which is the most misleading possible symptom.

4. Site settings can be handed to SiteWriter::write() under the 'settings' key as flat dotted keys ('printworks.products.requestPage' => 4) — SiteSettingsProvider resolves both that and the nested tree form. Worth stating, because the site set's own settings.yaml uses the nested form and there is no obvious reason the flat one would work too.

Also: CacheHashCalculator::generateForParameters() needs "&id=<pageId>" in the query string it is handed, and splitQueryStringToArray() decodes with rawurldecode(), which leaves "+" as a literal plus while the real request decodes it to a space. A hand-built test URL therefore has to be encoded with PHP_QUERY_RFC3986, or a value containing a space hashes differently than it arrives and the page answers 404 for a reason unrelated to the test.

## Query

typo3_architecture_lookup id="project-extension-tests"; typo3_task_guide checklist item "Cover the new behaviour with functional tests, not only unit tests."

## Suggestion

Extend the project-extension-tests hint with the frontend-functional-test facts above: the $coreExtensionsToLoad-follows-composer-require coupling; that SiteWriter::write() merges rather than replaces and that the site configuration file and the file-backed caches both survive the per-test database reset, so a class whose tests vary a site setting needs the file removed and CacheManager::flushCaches() called; that 'settings' accepts flat dotted keys; and the CacheHashCalculator encoding detail for tests that build a URL with a cache hash by hand. The common thread is worth saying outright — these all fail order-dependently and pass under --filter, so "it works alone" is not evidence.
