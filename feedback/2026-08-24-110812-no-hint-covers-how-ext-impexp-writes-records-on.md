---
date: 2026-08-24T11:08:12+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/typo3-cms
---

# No hint covers how EXT:impexp writes records on import; hint_lookup answers with test and distrib...

## Observation

Task: verify a bug report that EXT:impexp imports page translations onto the import target instead of onto their default language page, then write the fix and review it.

typo3_hint_lookup with paths ["typo3/sysext/impexp/Classes/Import.php", "typo3/sysext/impexp/Tests/Functional/Import/MultilingualPagesAndTtContentTest.php"] and task "import writes pages and page translations via DataHandler" returned six hints: core-tests, datahandler-basics, datahandler-testing, language-files, impexp-artifact, system-extension-boundaries. Five of them are about writing tests, about DataHandler in general, and about labels. The one that names impexp — impexp-artifact, "Writing the Export a Distribution Ships" — is about the console export options a distribution artifact needs (--include-related, --save-files-outside-export-file). None of them says anything about how the import writes what the export produced.

What the task actually needed, and what I read out of the checkout instead:

- header.pagetree in an export file is two things at once. Export.php:192 appends every page translation as its own top level node "to achieve" being counted inside the page tree, i.e. as a flat marker set. Import.php:819 reads the same section as a real tree through ImportExport::flatInversePageTree(), which reverses each level and hands top level nodes the pid -1. That double use is the whole defect and it is stated in neither file's docblock.
- Import::writePages() resolves a parent through $this->importNewIdPids, which is filled as pages are written (Import.php:825, 1084), so the write order decides whether a parent is resolvable at all.
- Import::addSingle() sets the pid only for new records and unsets it for existing ones (Import.php:1133-1141), which is why update mode cannot move a page and why writePagesOrder() exists.
- writePagesOrder() is guarded by $pagePid >= 0 (Import.php:907), so the -1 nodes are excluded from the correction pass as well.

Two adjacent questions in the same session I also answered from the checkout without asking this server, having assumed there was nothing to ask: which field carries a tt_content translation parent (l18n_parent, not l10n_parent — I found it through SQLite treating an unresolved double-quoted identifier as a string literal in my own debug dump, then confirmed in frontend/Configuration/TCA/tt_content.php:21), and what --update-records actually does (ImportCommand.php:60-64). typo3_schema_lookup would presumably have answered the first in one call; I read it and passed over it. For the second I did not try typo3_documentation_lookup, although EXT:impexp has a published manual.

## Query

typo3_hint_lookup with paths ["typo3/sysext/impexp/Classes/Import.php","typo3/sysext/impexp/Tests/Functional/Import/MultilingualPagesAndTtContentTest.php"], task "import writes pages and page translations via DataHandler", limit 10; and typo3_task_guide changeType "audit" with the same Classes/Import.php path

## Suggestion

A hint of its own for the impexp import path, next to impexp-artifact, which is about producing the artifact rather than reading one back. What it would have to carry: that header.pagetree is used as a tree by the import and as a flat marker set by the export, that page translations are appended to it as top level nodes, that flatInversePageTree() reverses each level and gives top level nodes the pid -1, that a page's parent is resolved through importNewIdPids and therefore depends on write order, that addSingle() sets a pid only for records it creates, and that writePagesOrder() is the update-mode correction pass and skips everything carrying -1. Any one of those sentences would have shortened the diagnosis; together they are the defect.
