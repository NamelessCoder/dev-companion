---
date: 2026-08-19T09:43:15+00:00
category: idea
status: open
model: claude-opus-5[1m]
tool: typo3_extension_describe, typo3_hint_lookup, typo3-extension-health
directory: /home/benji/projects/blog
---

# What kept me from four wrong findings: deprecatedFiles, the conditions hint, and the sinks rule

## Observation

Task: full audit of the blog extension before its official v14 release. Recording what worked, because it is what must not be broken later. Four concrete saves, each of which would otherwise have cost a wrong finding or a wrong fix.

1. typo3_extension_describe -> deprecatedFiles. It reported ext_tables.php (#109438) and ext_emconf.php (#108345) with the predicate and the cost, before I had opened a single file. Both survived to the final report as findings, and the ext_emconf one carried the exact condition — "composer.json declares neither providesPackages nor a version" — which is what made it actionable rather than a note. A file listing cannot produce either. This field is the highest-value thing the server gave me in the whole session.

2. typo3_hint_lookup id=typoscript-conditions. The extension's [blog.isPost()] condition is broken on v14 because BlogVariableProvider reads $GLOBALS['TSFE']->page. The hint said, in the same breath, that $GLOBALS['TYPO3_REQUEST'] is not the replacement because conditions are matched before the frontend request handler assigns it. Without that I would have proposed exactly that substitution — it is the obvious one — and it would have failed silently in the same way as the bug I was fixing. The hint also named AfterPageAndLanguageIsResolvedEvent and, separately, that `page` is among the variables the matcher is initialised with. Those two facts are what let me judge two competing fixes (a maintainer's branch and an open draft PR) as both technically sound, and reframe the choice as an API decision rather than a technical one. That was the most useful single answer of the session.

3. typo3_hint_lookup id=extbase-plugin-registration. It states that in v14 configurePlugin()'s plugin type argument must be omitted or "CType", and anything else throws. The extension passes ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT everywhere. That looked like a finding. I checked the constant in the installed core — it is 'CType' — and dropped the candidate. Without the hint I would not have looked; with only the hint I would have reported it. The pair is what produced the right answer.

4. typo3_hint_lookup id=security-sinks, reached through a paths-based query. Its rule — a finding about a user-controlled value is a claim about the sink, and a component that hands the value on is on the path rather than at the end of it — did two things. It made me follow the JSON-LD ViewHelpers into the template and find the real sink, which produced a confirmed XSS with the payload reproduced verbatim in a test. And it made me drop Nl2pViewHelper correctly: escapeOutput=false looked damning, but reading AbstractViewHelper::isChildrenEscapingEnabled() showed children are escaped precisely because output escaping is off. Both directions came from the same rule.

Also worth keeping: typo3_changelog_lookup returns the full tag list on every call, so the second call onward in a sweep is read off the first rather than guessed. And typo3_rule_lookup, called with a documentId I had mis-transcribed, returned matchCount 0 together with the complete document list — a failure that taught me the correct ids instead of just failing. That is a good failure mode and I would not change it.

## Query

typo3_extension_describe extension=blog; typo3_hint_lookup id=typoscript-conditions targetVersion=14; typo3_hint_lookup id=extbase-plugin-registration targetVersion=14; typo3_hint_lookup paths=["Classes/Domain/Validator/GoogleCaptchaValidator.php","Classes/Domain/Finisher/CommentFormFinisher.php","Classes/Controller/BackendController.php"] task="Security of a frontend comment form and a backend moderation module"

## Suggestion

Keep deprecatedFiles on typo3_extension_describe exactly as it is — predicate plus cost, and reported for files that are present rather than only when asked. If the same treatment can be extended to other whole-file predicates (a Configuration/ file a version now expects, one it no longer reads), that is the same value again.

Keep the paired shape of the hints that saved me: the rule and the reason it holds. typoscript-conditions works because it says what does not answer the question, not only what does. extbase-plugin-registration works because it states the constraint sharply enough to check against the installation. A hint that only said "use the event" would have been worth much less.

Do not make the hints terser. Their length is what carries the "and not this" half.
