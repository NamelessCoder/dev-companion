---
date: 2026-08-03T16:28:26+00:00
category: missing-knowledge
status: open
model: claude-opus-5[1m]
tool: typo3_script_lookup, typo3_task_guide
directory: /home/benji/projects/ext-guidedtour
---

# Task: install TYPO3 14.3.5 unattended from a shell script so that "ddev start" sets the instance ...

## Observation

Task: install TYPO3 14.3.5 unattended from a shell script so that "ddev start" sets the instance up on its own. The "typo3 setup" command has four behaviours that its own --help does not reveal and that cost several failed runs.

1. The SQLite driver is called "sqlite", not "pdo_sqlite". TYPO3_DB_DRIVER=pdo_sqlite — the value TYPO3 itself then writes into config/system/settings.php — is rejected with 'The connection type "pdo_sqlite" does not exist. Please use one of the following mysqli, mysqliSocket, pdoMysql, pdoMysqlSocket, postgres, sqlite'. So the accepted input name and the persisted name differ, and the persisted one is the misleading one. With driver "sqlite" no host, port, dbname, username or password is needed at all; the database file is created at var/sqlite/cms-<hash>.sqlite.

2. --distribution and --create-site are mutually exclusive: using both raises RuntimeException 1775034289 "The --distribution and --create-site commandline options may not be used at the same time".

3. Far more consequential: in Composer mode both options are silently inert. SetupCommand only evaluates them when $distributions['active'] === [], and SetupService::getAvailableDistributions() classifies by PackageManager::isPackageActive(). Every required Composer package is always active, so requiring any package that ships Initialisation/data.xml (typo3/theme-camino here) puts a distribution into 'active' and the command answers "The --distribution and --create-site commandline options have no effect, when distributions are already active" — and creates no site configuration at all. SetupService::activateDistributionPackage() even carries the comment "We don't end up here in Composer mode". The practical consequence is the opposite of what the help text suggests: TYPO3_SETUP_DISTRIBUTION does nothing, and the distribution's pages, content, files and site configuration are imported by the *subsequent* "typo3 extension:setup" via ImportContentOnPackageInitialization. Requiring the distribution package and running extension:setup is the whole mechanism; the setup option is noise. And because create-site is inert for the same reason, an installation seeded this way has no way to be told its own URL.

4. "typo3 setup" refuses a database that is not empty: "The selected database contains already 40 tables. Please delete all tables or select another database." A re-runnable install script must therefore either guard on config/system/settings.php or drop the database first; --force only overwrites the settings files, not the schema.

Working order, verified from a cold start: typo3 setup (settings.php, admin user, schema) → typo3 extension:setup (extension setup plus the distribution import) → typo3 cache:flush.

## Query

Installing TYPO3 14.3 non-interactively with "typo3 setup" in Composer mode: driver names, seeding a distribution, and what happens on a re-run.

## Suggestion

Describe the non-interactive install path for the current major: the accepted driver names against the ones written to settings.php, which TYPO3_* environment variables each option reads, and above all that --distribution and --create-site are inert in Composer mode because every required package counts as active — with the consequence that a distribution is seeded by requiring its package and running "typo3 extension:setup", and that no site configuration carrying the installation's own URL can be produced by the setup command in that situation. Include that the command aborts on a non-empty database, so an idempotent installer needs its own guard.
