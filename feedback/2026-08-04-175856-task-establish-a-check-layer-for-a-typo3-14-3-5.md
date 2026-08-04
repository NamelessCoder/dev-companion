---
date: 2026-08-04T17:58:56+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3-extension-testing, typo3_hint_lookup
directory: /home/benji/projects/site-new
---

# Task: establish a check layer for a TYPO3 14.3.5 project, including a check for the XLIFF files i...

## Observation

Task: establish a check layer for a TYPO3 14.3.5 project, including a check for the XLIFF files it ships.

static-quality.md asks for "XML well-formedness for the XLIFF files" and names no tool, unlike the neighbouring rows which name j13k/yaml-lint, helmich/typo3-typoscript-lint, eslint, stylelint. So I wrote well-formedness checking by hand with DOMDocument. The user pointed out that symfony/translation was already installed and ships XliffLintCommand, which validates against the XLIFF XSD rather than merely parsing — strictly better than what I wrote, and already in vendor/ on any TYPO3 project.

The TYPO3-specific trap, which nothing outside this server will ever tell you: XliffLintCommand's constructor takes requireStrictFileNames and it defaults to true. Strict means Symfony's own convention, a locale SUFFIX — messages.de.xlf. TYPO3 derives a translation domain from a locale PREFIX — de.messages.xlf. Left at its default, the linter fails every correctly named TYPO3 translation file. I found this by reading vendor/symfony/translation/Command/XliffLintCommand.php.

What stayed worth hand-writing was only what XLIFF itself does not know: that a translation declares target-language (the silent-discard failure the language-files hint documents), that a source is authored in English, and that a translation carries no unit its source dropped.

## Query

typo3-extension-testing references/static-quality.md, "Shipped configuration and data": "XML well-formedness for the XLIFF files" — applied to a sitepackage with 4 source and 4 de.-prefixed XLF files

## Suggestion

Name symfony/translation's XliffLintCommand in the "Shipped configuration and data" row as the XLIFF answer, note that it is usually already installed on a TYPO3 project so it costs no new dependency, and state the requireStrictFileNames=false requirement with the reason (Symfony suffix naming vs TYPO3 locale-prefix naming). Also worth saying that it covers schema validity but not the TYPO3 rules — target-language, English source — so a project still owns those.
