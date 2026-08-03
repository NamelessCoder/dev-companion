# Write what `typo3 setup` takes and what it refuses

**Serves:** feedback/2026-08-03-162826-task-install-typo3-14-3-5-unattended-from-a.md
**Priority:** normal

Step 1a on the mechanics and step 2 on the two site options, judged in
`D-KNW-046`, which carries the evidence and the 14.3 reading. Write one hint —
`installation-setup` is the subject and `knowledge/hints/configuration.json`
holds the neighbouring environment entries — saying that `--driver` takes
`sqlite` while `pdo_sqlite` is what lands in `config/system/settings.php` and
what `TYPO3_DB_DRIVER` is therefore rejected with, that the sqlite path is
calculated into `var/sqlite/` and needs no host, name or credentials, that the
command aborts where the database already has tables so an idempotent installer
guards on `settings.php` itself, and that `--distribution` and `--create-site`
are inert in Composer mode whenever any required package ships an
`Initialisation/` data file — with the consequence the feedback paid for, that
no site configuration is written and the installation cannot be told its own
URL. Read `SetupCommand::$connectionLabels`,
`SetupDatabaseService::getDriverOptions()` and the `1669747200` validator on
`.checkouts/12.4`, `13.4` and `main` before writing, because the entry claims one
statement across all four and `D-KNW-046` assumes that without having checked it.
Give `installation-setup` in `knowledge/task-intents.json` a checklist line
pointing at it, and confirm with `bin/cli hints:probe` on the feedback's own
query that an install task now reaches it.
