# Say which environment variables the core reads itself

**Serves:** feedback/2026-07-31-185900-i-had-to-establish-from-my-own-knowledge.md
**Priority:** normal

Step 1a, and the corpus states the project half without the core half: nothing
below `knowledge/` or `skills/` contains `%env(`, `getenv` or `TYPO3_CONTEXT`,
while `project-repository-layout` already tells a project to read deployment
secrets from the environment. Write the missing half into
`knowledge/architecture-hints/general.json`, having re-read all four branches
under `.checkouts/` first: the variables `SystemEnvironmentBuilder` reads
(`TYPO3_CONTEXT`, `TYPO3_PATH_ROOT`, `TYPO3_PATH_APP`, each also as `REDIRECT_`
and the context also as `HTTP_`); that `%env()` is a YAML placeholder resolved
by `YamlFileLoader`, so it reaches site configuration and services but not
`config/system/`; and that everything else, the encryption key and the database
credentials included, is the project's own `getenv()` in `additional.php`. One
half is bound `since: 15` — `Breaking-110319` moves the empty-key check ahead of
`ext_localconf.php` loading, so an installation that sets the key from an
extension throws. Whether this extends `configuration-reach`, extends
`project-repository-layout` or becomes a hint of its own is decided from what
that reading shows. Add the assertion beside
`HintsTest::projectSystemConfigurationStatesItsOwnershipBoundary`, which holds
the requirement this completes, `R-KNW-032`.
