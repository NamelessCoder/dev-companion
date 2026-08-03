---
date: 2026-08-03T16:46:59+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_hint_lookup, typo3_task_guide
directory: /home/benji/projects/ext-guidedtour
---

# Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 insta...

## Observation

Task: full conformance audit of the project extension EXT:guidedtour against a TYPO3 14.3.5 installation.

The highest-impact finding of the whole audit came from reading installed source, not from this server. EXT:guidedtour's Resources/Private/Language/de.locallang.xlf has a `<file>` element with no attributes at all. TYPO3\CMS\Core\Localization\Loader\XliffLoader:130 decides on exactly one of them:

    $isDefaultLanguage = !isset($fileTag['target-language']);

With no target-language the file is treated as a default-language template and line 145 takes `<source>` instead of `<target>`. All 22 German translations are silently discarded — no error, no deprecation, no log entry. German backend users see English while a fully maintained translation file sits in the package.

The knowledge is half-present. The `language-files` hint does state that a locale-prefixed file's file element "declares source-language="en" target-language="de" and points original= at the source path" — but only inside the remedy for correcting a non-English *source* file. It is not stated as a requirement that holds for any translation file, and the failure mode is stated nowhere. A hint read as guidance for writing a new label file never fires on an existing translation file that is already wrong, which is the direction the conformance skill explicitly asks rules to be read in.

Establishing it cost four hops into installed source, each of which the server could have short-circuited: grep XliffParser (which turned out to carry `@deprecated ... Switch to Symfony Translation loaders` and is not the active path in 14.3 — so reading it answers the wrong question), grep Configuration/DefaultConfiguration.php for LANG.loader, list Classes/Localization/Loader/, read XliffLoader.php. Which loader parses XLF in this installation is an installation fact, which is what this server exists for.

## Query

typo3_task_guide {task: "Full conformance audit ...", paths: [..., "Resources/Private/Language/locallang.xlf"], targetVersion: "14.3", changeType: "audit"} → hint id `language-files`

## Suggestion

Add a standalone statement to the `language-files` hint, phrased as a defect to detect rather than only as a rule for writing: a locale-prefixed XLF whose `<file>` element lacks target-language is parsed as the default language and its `<target>` values are discarded silently — no error, no deprecation, the labels simply render in the source language. Name XliffLoader::parseXliff1() as where this is decided in v14, and say that XliffParser is the deprecated path so reading it answers a different question. Worth adding beside it: in XLIFF 1.2 `original`, `source-language` and `datatype` are required attributes on `<file>`, so an XLF schema check in a project's check layer catches this entire class of defect — which connects the finding to the testing workflow instead of leaving it a one-off.
