# No way to ask whether a deprecation's migration target exists on the other declared major

**Serves:** feedback/2026-08-19-094221-no-way-to-ask-whether-a-deprecation-s-migration.md, D-VER-009
**Priority:** normal

Judged as step 2, delivery: the call that answers this is one
`typo3_changelog_lookup` on the deprecation's own issue number, and nothing says
so where the sweep passes. Put it there. In `skills/base.md` step 5, after the
sentence sending an identifier to that tool, say that a returned deprecation's
`issue` is a query of its own, that it returns the sibling entries the core
announced the replacement in, and that their version is what settles whether the
replacement is on the lower declared major. Say there too that a deprecation
with no sibling goes to
`extension/compatibility/a-declared-major-that-is-not-installed`, which already
covers it. In the `query` parameter of `src/Tool/ChangelogLookup.php`, say that
the issue number is among the words the file name carries. Add a `routing` entry
to `knowledge/server-scope.json` for sweeping deprecations under more than one
declared major: the one that reaches that document today fires on writing
against a core API, and a sweep is reading. Then give `ChangelogLookupTest` the
assertion that an issue number reaches every entry filed under it. `D-VER-009`
has the measurements, the boundary and what was rejected; archive the feedback
in the same commit.
