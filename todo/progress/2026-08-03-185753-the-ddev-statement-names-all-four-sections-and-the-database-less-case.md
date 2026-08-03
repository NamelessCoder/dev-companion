# The DDEV statement names all four sections it writes and the database-less case

**Serves:** R-KNW-060, feedback/2026-08-03-162858-task-run-a-local-typo3-14-3-5-development.md
**Priority:** normal
**Branch:** todo/the-ddev-statement-names-all-four-sections-and-the-database-less-case
**Claimed:** 2026-08-03

Establish from DDEV's own documentation and sources at v1.25.1 what its TYPO3
settings management generates when the database container is omitted — the
generated file read for `D-KNW-049` came from a project that has one — then
rewrite the third and fourth statement of `project-configuration-files` in
`knowledge/hints/project.json` so they name all four sections it writes (`DB`,
`GFX`, `MAIL`, and `SYS` with `trustedHostsPattern`, `devIPmask` and
`displayErrors`), say that taking the file over means supplying the three
non-database ones, and say that an installation on SQLite or with
`omit_containers: [db]` has only the marker route because the generated `DB`
block merges over `settings.php`. Add the words a session in that situation
searches with to `appliesTo`, and an assertion beside
`HintsTest::projectSystemConfigurationStatesItsOwnershipBoundary`.
