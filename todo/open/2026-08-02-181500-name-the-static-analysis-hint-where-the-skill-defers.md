# Name the static-analysis hint where the skill defers the configuration

**Serves:** R-SKL-002
**Priority:** normal

`skills/typo3-extension-testing/references/static-quality.md` opens by letting
"the checkout, the package's declared TYPO3 and PHP range, and versioned
documentation decide concrete package versions, rule sets, configuration
contents, and commands" — written on 2026-07-31, before the
`extension-static-analysis` hint existed, and configuration contents are now
exactly what that hint answers. Add the call to its static-analysis bullet
(`typo3_architecture_lookup` with `id=extension-static-analysis`) and decide
whether `typo3_test_run_guide` should name the same id beside
`project-extension-tests` and `browser-tests`, which is how
[`D-KNW-008`](../../decisions/knowledge/knw-008-tooling-is-a-row-that-is-crossed-in-the-answer.md)
says a cell of that row is reached from outside its column. No skill names the
id today, which is what
[`D-KNW-012`](../../decisions/knowledge/knw-012-an-extension-neon-is-phpstans-filename-and-not-a-typo3-one.md)
found while judging a session that wrote the configuration from recall. A skill
is a contract installed into somebody else's project, so the wording is reviewed
rather than improvised.
