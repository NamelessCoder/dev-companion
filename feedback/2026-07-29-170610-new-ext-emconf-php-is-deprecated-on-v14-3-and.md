---
date: 2026-07-29T17:06:10+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_lookup
directory: /home/benji/projects/site-new
---

# NEW: ext_emconf.php is deprecated on v14.3 and the hint that lists an extension's registration fi...

## Observation

NEW: ext_emconf.php is deprecated on v14.3 and the hint that lists an extension's registration files does not mention it.

extension-files is exactly where someone asks "which files does an extension need". It currently covers ext_tables.sql, Configuration/JavaScriptModules.php, Configuration/Icons.php, Configuration/page.tsconfig, Configuration/user.tsconfig and ext_localconf.php — all accurate, and ext_emconf.php is simply absent. Absent reads as "not relevant", which is nearly right but leaves out the part that costs something.

Deprecation #108345 (v14.3): an extension that ships ext_emconf.php and does not declare the metadata in composer.json triggers a deprecation during cache warm-up. The minimum is extra.typo3/cms.version plus extra.typo3/cms.Package.providesPackages. In v15 the file is no longer evaluated at all. For a Composer-only project the sensible move is to delete it outright — the dependency list it carried is already in composer.require, and keeping both means keeping two lists in sync.

How I found it is itself the argument for stating it: I did not read the changelog, the first functional test run did. phpunit runs with failOnDeprecation, the sitepackage still had an ext_emconf.php from before, and the deprecation turned a green suite red on the very first attempt. Without a test suite this sits in the deprecation log until v15 breaks the installation.

One detail worth carrying with it, because it is not obvious from the changelog: with ext_emconf.php gone, the extension title comes from composer.json "description" split on the first " - " — Package::createPackageMetaData() takes the part before it as the title and the rest as the description. An extension that just deletes the file ends up with its whole description as its backend title.</observation>
<parameter name="suggestion">Add ext_emconf.php to extension-files: deprecated since 14.3 (#108345), not evaluated from v15, replaced by extra.typo3/cms.version and extra.typo3/cms.Package.providesPackages in composer.json, and simply deletable in a Composer-only project. Include the " - " split that derives the title from the description, since that is what an extension notices right after deleting the file.

## Query

typo3_architecture_lookup id=extension-files, targetVersion=14.3 — the hint lists ext_tables.sql, JavaScriptModules.php, Icons.php, page.tsconfig, user.tsconfig and ext_localconf.php, and does not mention ext_emconf.php at all
