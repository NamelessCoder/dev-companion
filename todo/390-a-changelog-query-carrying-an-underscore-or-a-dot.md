# A changelog query carrying an underscore or a dot reaches nothing

**Serves:** R-ANS-004, feedback/

`typo3_changelog_lookup` says its query is "words the entry has to carry,
matched against its title", and it is matched against neither. `Changelog::entries()`
offers the file name and its CamelCase split, `LabelSearch::terms()` splits the
query on whitespace alone, and `carryingEvery()` asks `str_contains` — so
`ext_tables.php` reaches no entry while *ext_tables.php in extensions* sits in
`.checkouts/14.3`, and `ext_emconf.php`, `list_type`, `backend_layout`,
`SC_OPTIONS` and `mod.web_layout` all reach nothing for the same reason. That is
what `feedback/2026-07-31-172753-…` and `feedback/2026-07-31-194504-…` report from
two checkouts, and `D-ANS-003`'s reading of 2026-08-02 reproduced every one of
them. The step is to make the identifier findable — split a term on `_`, `.` and
`-` as the file name already is, or search the title `Changelog::read()` already
parses — with a test per shape beside the `PackageSourcesTest` changelog ones,
and to archive both feedback in the same commit. What it must not become is a
second matcher: `D-DIS-003` and `R-ANS-004` are why one rule serves the label search
and this one.
