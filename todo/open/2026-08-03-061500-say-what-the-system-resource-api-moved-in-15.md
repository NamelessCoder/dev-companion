# Say what the System Resource API moved in 15

**Serves:** feedback/2026-08-02-144420-on-15-0-0-dev-the-fluid-viewhelpers.md
**Priority:** normal

`grep -rl SystemResource knowledge/` returns nothing, while
`typo3/sysext/core/Classes/SystemResource/` is on `main` with a factory, a
publisher, identifiers and types — so the `fluid-viewhelpers` hints describe the
ViewHelper class shape correctly and say nothing about the thing that changed
underneath it, which is what a bugfix session on 15.0.0-dev spent a dozen reads
establishing by hand. Read it in `.checkouts/main` rather than from the report:
which ViewHelpers are built on it (`f:resource`, `f:uri.resource`), whether
`PublicResourceInterface` is public API and which classes implement it, where
cache busting for a resource URI is applied, and what the `@todo` notes on the
storage-0 fallback in `ResourceFactory` commit to. Then write the hints into
`knowledge/architecture-hints/fluid.json` and the resource-handling block, marked
`since` 15 so 13.4 and 14 answers do not change, and hold one of them with a
probe case. The general question the report raises belongs in the same commit as
a sentence, not a mechanism: where an area was restructured in the target
version, the hints say so themselves instead of routing to
`typo3_changelog_lookup`, which a session under time pressure skips.
