---
date: 2026-08-04T17:58:40+00:00
category: missing-knowledge
status: open
model: claude-opus-5
tool: typo3-extension-testing, typo3_hint_lookup
directory: /home/benji/projects/site-new
---

# Task: establish a complete check layer for a TYPO3 14.3.5 project whose only declared commands we...

## Observation

Task: establish a complete check layer for a TYPO3 14.3.5 project whose only declared commands were two PHPUnit suites and Playwright.

references/static-quality.md names helmich/typo3-typoscript-lint for TypoScript. It cannot be installed on a v14 project. Its newest release v3.3.0 AND dev-master both require symfony/event-dispatcher ^5.4 || ^6.4 || ^7.0, while a TYPO3 14.3 install carries symfony/event-dispatcher 8.1.2. Composer refuses both. I verified dev-master explicitly with a --dry-run require, so this is not a "wait for the next release" situation as far as the published branches go.

On the first pass I reported the TypoScript surface as an unfillable gap and wrote that into the project readme. The user pushed back — "wir sollten auch einen typoscript linter einsetzen" — and the answer turned out to be an isolated tool install: a second composer.json under Build/typoscript-lint with its own vendor/, resolving Symfony 7.4 for the tool while the site stays on 8. Nothing in the skill or in any hint carries that pattern, and it is the general answer whenever a check tool's constraints cannot meet the project's.

Two further things I had to read out of the tool's own source, both of which change the configuration you write:
- ConfigurationLocator always loads the tool's own typoscript-lint.dist.yml first and merges the project config over it, so a sniff cannot be switched off by leaving it out of the list; it needs `disabled: true`.
- DuplicateAssignment and RepeatingRValue both advise extracting a repeated value into a TypoScript *constant*. On v13+ that is wrong advice: site set settings replaced constants, so following it reintroduces the mechanism a modern sitepackage was built without.

I also had to check whether the core could stand in: it ships LosslessTokenizer/LossyTokenizer and an AST builder, but TokenType has no invalid/error case, so a core-parser-based lint would catch almost nothing. That was worth knowing and cost a read of TokenType.php.</observation>
<parameter name="suggestion">In static-quality.md: (1) state that helmich/typo3-typoscript-lint does not resolve against a Symfony 8 stack, so on TYPO3 14 it is an isolated install rather than a require-dev; (2) carry the isolated-tool-install pattern as a named option in "Establish one command per check", since it applies to any tool whose constraints cannot meet the project's; (3) note the dist-config merge (disabled: true, not omission) and that the two duplicate-value sniffs give constants-era advice. Optionally note that the core's own tokenizer is not a substitute because it has no error token.</suggestion>
</invoke>

## Query

typo3-extension-testing references/static-quality.md, "Shipped configuration and data" row naming helmich/typo3-typoscript-lint, applied to a TYPO3 14.3.5 Composer project
