# State what the impexp import rewrites in a site configuration

**Serves:** feedback/2026-08-03-162836-task-seed-a-local-typo3-14-3-5-development.md
**Priority:** normal
**Branch:** todo/state-what-the-impexp-import-rewrites-in-a-site
**Claimed:** 2026-08-03

`D-KNW-048` is the judgement this comes out of, and it names what was read. Write into `knowledge/hints/distribution.json` what
`Import::processSiteConfigurations()` does to a site configuration carried
inside an export file: it overwrites `base` with `/<identifier>/` and discards
the exported value, so a distribution seeded by `typo3 extension:setup` answers
404 at the project root until `config/sites/<identifier>/config.yaml` is
corrected by hand. Bind it `since: 14` — the feature arrived in 14.2 as
`Feature-109340-IncludeSiteConfigurationsInImportExport.rst` and exists in
neither `.checkouts/12.4` nor `13.4`. The reading is
`typo3/sysext/impexp/Classes/Import.php` in `.checkouts/14.3` at `faf60eea22`,
lines 438 and 454 to 500, with `.checkouts/main` beside it. Two neighbours come
out of the same reading and belong in the same change. The method does not run
at all for a non-admin importing user, nor for a package shipping
`Initialisation/Site/`, where
`ImportSiteConfigurationsOnPackageInitialization` takes over and leaves the
shipped `base` alone. And two statements say only `rootPageId` is remapped —
the second bullet of `initial-content-references` in the same file, and the
`limitToPages` bullet of `record-routing` in `knowledge/hints/records.json` —
which holds for that route and not for this one, so each says which route it
means. Give the entry `appliesTo` words a session with a root-level 404 would
reach for, then check the feedback's own query against it with
`bin/cli hints:probe`. Hold it with an assertion in `tests/Unit/HintsTest.php`
beside `theSeedingAnswerNamesImpexpAsTheWayATreeIsEstablishedAgain`, and a
requirement in `requirements/knowledge/` naming that test.
