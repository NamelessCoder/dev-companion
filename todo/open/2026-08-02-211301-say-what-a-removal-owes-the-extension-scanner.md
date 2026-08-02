# Say what a removal owes the extension scanner, where a breaking change is read

**Serves:** feedback/2026-08-01-115109-reviewing-the-core-patch-replace-gd-based-error.md, R-ANS-017
**Priority:** low

Step 2 of the ladder, on the queries in
[`D-ANS-029`](../../decisions/answers/ans-029-the-scanner-matcher-is-stated-on-the-route-a-removal-takes.md):
the matcher sentence is in the corpus under `## Deprecations`, and
`## Breaking Changes` — which both `typo3_rule_lookup` and the `breaking`
intent's own `rulesQuery` return — states the `[!!!]` marker and the RST file
and stops.

Establish against `.checkouts/` what the core actually requires before writing
anything: whether every removed public method gets a `MethodCallMatcher.php`
entry or only the statically findable ones, which of the matcher files below
`typo3/sysext/install/Configuration/ExtensionScanner/Php/` a removal belongs in,
and how the entry's `restFiles` relates to the changelog entry's `FullyScanned`,
`PartiallyScanned` or `NotScanned` tag. `Breaking-101955-RemovedPublicMethodsRelatedToImageGeneration.rst`
and `MethodCallMatcher.php:5969` in `.checkouts/main` are the worked example,
and `typo3/sysext/core/Documentation/Changelog/Howto.rst` is the authority on
the tag. Then write what holds into `## Breaking Changes` of
`knowledge/documents/typo3-commit-messages.md`, and settle in the same reading
whether the `breaking` intent's "consider an extension scanner matcher" stays a
recommendation.
