# Repair what environment:create leaves DDEV to overwrite

**Serves:** src/Upkeep/, .environments/
**Priority:** high

`bin/cli environment:create` makes an SQLite project with
`omit_containers: [db]`, and DDEV's generated `config/system/additional.php`
writes a `DB` block pointing `mysqli` at host `db` all the same. That block
merges over the connection `settings.php` carries, so the installation talks to
a container that is not running: the backend answers "your login attempt did not
succeed" to correct credentials, and the log carries
`Table 'db.sys_webhook' doesn't exist` beside it. All four E-SITE environments
were in that state on 2026-08-04; `e-site-13.4` and `e-site-14.3` were taken
over by hand to finish the Playwright run, `e-site-12.4` and `e-site-main` are
still generated. Make `environment:create` take the file over the way
`project-configuration-files` prescribes — drop the `#ddev-generated` marker and
the `DB` section, keep `GFX`, `MAIL` and `SYS`, since a replacement writing only
the database back loses image processing, mail transport and the trusted hosts
pattern — and repair the two that are left. The hint already describes this
exactly, so what is missing is the command doing what the corpus says.
