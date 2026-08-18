---
date: 2026-08-18T07:41:24+00:00
category: tool-gap
status: open
model: claude-opus-5[1m]
tool: typo3_changelog_lookup, typo3_documentation_lookup, typo3_project_describe
directory: /home/benji/projects/blog
---

# Nothing answers about an API at a TYPO3 major other than the installed one

## Observation

While fixing t3g/blog I wrote code against TYPO3\CMS\Core\Schema\TcaSchemaFactory, TcaSchemaCapability::getSystemCapabilities(), LanguageAwareSchemaCapability::getTranslationSourceField(), FieldCapability/SystemInternalFieldCapability::getFieldName() and RecordFactory::createResolvedRecordFromDatabaseRow(). typo3_project_describe reported coreConstraint ^13.4.15 || ^14.3 || 15.*.*@dev, so the code has to run on 13.4 as well, but the installation on disk is 14.3.6.

typo3_changelog_lookup with "TcaSchemaFactory" returned three entries, the useful one being Feature-104002-SchemaAPI at 13.2. That establishes the API was introduced before 13.4 and nothing more: it does not say whether getSystemCapabilities() exists there, whether SYSTEM_CAPABILITIES holds the same ten entries, or whether getTranslationSourceField() is nullable on that line. The tool description says versions above the installed major are read from docs.typo3.org, so the covered direction is upward; the extension's question was downward, against a major it supports but does not have installed.

I settled it by curl-ing five files from https://raw.githubusercontent.com/TYPO3/typo3/13.4/typo3/sysext/core/Classes/Schema/ plus Domain/RecordFactory.php and diffing the method lists against the installed 14.3 copies. That answered it exactly and is entirely outside this server. I then also built a second installation under var/v13 with composer require typo3/cms-core:^13.4 and ran the suite there, which is the right proof but costs a full core download; the source comparison alone would have been enough to decide the API question before writing a line.

This is not an edge case for this server's audience: nearly every extension or sitepackage repository declares more than one major, and typo3_project_describe already reports that constraint. The reading is currently the caller's, done off-server, and a session that skips it ships code that fatals on the other declared major.

## Query

Task: "ich habe das blog setup ausgeführt, aber das frontend zeigt immer noch 404", which turned into fixing a v14 rendering crash in an extension declaring typo3/cms-core ^13.4.15 || ^14.3 || 15.*.*@dev. Called typo3_changelog_lookup with query "TcaSchemaFactory", no type, no version.

## Suggestion

Answer "does this identifier exist, and with what shape, at version N" for the majors the project declares, not only for the one installed. Given a class or method name and a targetVersion, report whether it exists on that line, its signature and its nullability, read from the released sources for that branch the way the manual is already read from docs.typo3.org above the installed major. Failing that: typo3_project_describe already knows the declared constraint, so a lookup that can only answer for the installed version could say so — "this holds for 14.3; the project also declares 13.4, which is not installed here" — instead of leaving the caller to notice.
