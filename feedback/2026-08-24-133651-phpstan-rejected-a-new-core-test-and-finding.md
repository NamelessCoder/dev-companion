---
date: 2026-08-24T13:36:51+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_rule_lookup, typo3_hint_lookup, typo3_test_run_guide
directory: /home/benji/projects/typo3-cms
---

# PHPStan rejected a new Core test and finding the codebase's idiom cost four round trips

## Observation

Task: add functional tests to a TYPO3 Core patch and get the project's own checks green.

My new test in typo3/sysext/core/Tests/Functional/TypoScript/IncludeTree/SysTemplateTreeBuilderTest.php obtained the TypoScript cache the way the file already did for a flush — $this->get(CacheManager::class)->getCache('typoscript') — and passed it to a parameter typed PhpFrontend|null. CacheManager::getCache() returns FrontendInterface, so PHPStan failed with two argument.type errors. Nothing in the failure tells you which of several legitimate fixes this codebase actually uses.

Recovering that cost four round trips: grep the composer.json and Build/phpstan/phpstan.neon to establish that phpstan-phpunit is installed (so self::assertInstanceOf would narrow); grep the sysexts for existing getCache('typoscript') call sites; read typo3/sysext/core/Classes/TypoScript/TypoScriptStringFactory.php around the one that matched; then apply what it does. The answer is a plain /** @var PhpFrontend $cache */ above the assignment, which is what Core does in production code and what I copied. I also had to check that Build/phpstan/phpstan-baseline.neon carried no entry for SysTemplateTreeBuilder before touching it, since AGENTS.md forbids adding to the baseline — that grep returned nothing, which was the answer I wanted but reads identically to a failed search.

I did not put any of this to the server. Looking at the tool list afterwards, typo3_rule_lookup ("the whole procedure, not one fact out of it", keyed by a documentId from typo3_project_describe) and typo3_hint_lookup are the plausible homes, and typo3_test_run_guide is not — that one reads as "how do I invoke the suite", which I already knew from AGENTS.md. So the routing between hint and rule for a question of the form "what is this codebase's idiom for X" was not clear to me from the names, and with a checkout open and grep available the cheaper-looking move was to grep. It was not actually cheaper.

One adjacent cost from the same guessing habit, in the same session: I ran ./Build/Scripts/runTests.sh -s functional over a path list that included typo3/sysext/tstemplate/Tests/Functional, which does not exist. The whole container run died on "Test file not found" and had to be repeated with the path removed. An ls would have caught it; so would anything that knows which sysexts carry a functional suite.

## Query

Never called. The failure, from ./Build/Scripts/runTests.sh -s phpstan: "Parameter #5 $cache of method TYPO3\CMS\Core\TypoScript\IncludeTree\SysTemplateTreeBuilder::getTreeBySysTemplateRowsAndSite() expects TYPO3\CMS\Core\Cache\Frontend\PhpFrontend|null, TYPO3\CMS\Core\Cache\Frontend\FrontendInterface given." twice, from a functional test doing $cache = $this->get(CacheManager::class)->getCache('typoscript');

## Suggestion

An answer keyed on "Core test and static-analysis idioms that PHPStan level 5 enforces here" would have replaced four calls with one. The specific entry I needed: CacheManager::getCache() returns FrontendInterface, and where a PhpFrontend or VariableFrontend is required, Core narrows with a /** @var */ annotation at the assignment rather than an instanceof assertion or a cast — with TypoScriptStringFactory named as the reference call site. Sitting beside it, the two rules from AGENTS.md that bear on the same moment: do not add to Build/phpstan/phpstan-baseline.neon, and regenerate it only after a PHPStan update.

Whichever of typo3_rule_lookup and typo3_hint_lookup owns that, the descriptions could make the split legible from the names alone. As they read now, "hint" and "rule" both plausibly answer "how does this codebase do X", and a caller with a checkout open will grep instead of choosing between them.
