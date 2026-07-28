---
date: 2026-07-28T15:04:44+00:00
category: missing-knowledge
status: open
tool: typo3_architecture_hint
---

# TypoScript, TSconfig and site sets are not covered. typo3_rule_lookup returned matchCount 0 and n...

## Observation

TypoScript, TSconfig and site sets are not covered. typo3_rule_lookup returned matchCount 0 and no document has a matching topic. typo3_architecture_hint for a setup.typoscript, a TypoScript AST class and a page.tsconfig returned System Extension Boundaries, TCA/FormEngine and — for the third slot — "CSS Browser Target", a section about evergreen browser baselines. This is the same path classification failure seen with .ts and .fluid.html: .typoscript and .tsconfig fall into the generic frontend domain and get answered with CSS. The server scope does declare TypoScript authoring out of scope, but that exclusion is about configuring an installation; shipping TypoScript defaults from a system extension is core contribution work and is what the paths above are. Verified at the pinned revision: (1) site sets are the current mechanism, 10 of them in core under typo3/sysext/*/Configuration/Sets/<SetName>/, each with config.yaml carrying a composer-style name (e.g. "typo3/fluid-styled-content"), setup.typoscript, labels.xlf and settings.definitions.yaml — note that settings.definitions.yaml, not constants.typoscript, is the replacement for TypoScript constants, which an agent will get wrong; (2) the set's labels.xlf maps to the domain <ext>.sets.<setname> per TranslationDomainMapper, which ties into the domain-derivation gap reported separately; (3) TSconfig is auto-loaded from Configuration/page.tsconfig and Configuration/user.tsconfig — present in a dozen sysexts — instead of being registered in ext_localconf.php; (4) the legacy registration API has not disappeared: ExtensionManagementUtility::addTypoScriptSetup() is still called from felogin, extbase and seo ext_localconf.php and carries an includeInSiteSets flag, so both mechanisms coexist and the server should say which one new code uses; (5) the file extension is .typoscript — 203 files against 4 remaining legacy .txt.

## Query

typo3_rule_lookup query="TypoScript setup constants site sets tsconfig" (0 matches); typo3_architecture_hint paths=["typo3/sysext/frontend/Configuration/Sets/Fluid/setup.typoscript","typo3/sysext/core/Classes/TypoScript/AST/AstBuilder.php","typo3/sysext/form/Configuration/page.tsconfig"]

## Suggestion

Add a TypoScript and TSconfig architecture section matched on Configuration/Sets/**, **/*.typoscript, **/*.tsconfig and Classes/TypoScript/**, and stop routing those paths to the CSS sections. It should state: site sets are how a system extension ships TypoScript, with the config.yaml / setup.typoscript / settings.definitions.yaml / labels.xlf layout and the dependency declaration between sets; settings.definitions.yaml supersedes TypoScript constants; page.tsconfig and user.tsconfig in Configuration/ are auto-loaded; addStaticFile and addTypoScriptSetup are the legacy path, still present but not for new code; the .typoscript extension; and that the TypoScript parser under typo3/sysext/core/Classes/TypoScript/ is covered by functional tests. Given how consistently the frontend domain misroutes, the underlying fix is worth doing once: classify by file extension into typescript, css, fluid, typoscript and yaml instead of one frontend bucket.
