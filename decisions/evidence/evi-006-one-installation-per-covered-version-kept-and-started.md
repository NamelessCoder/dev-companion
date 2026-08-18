---
id: D-EVI-006
date: 2026-08-03
status: open
---

# D-EVI-006 — One installation per covered version, kept and started

**`bin/cli environment:create E-SITE <version>` makes one installation per
covered version, each its own DDEV project, and starts one that is already there
rather than building it again.**

One installation runs one version, and there was one installation. So every case
naming `E-SITE` ran on the covered stable major, and what a client on another
covered line would be answered was shown by nothing.

## Evidence

- Twenty contract cases name `E-SITE`, and one of them names a version:
  `SITE-02` is `E-SITE` on the previous major. No case names 12.4 or the
  development line, so the demand the scenarios carry today is two installations
  rather than four.
- What one costs, measured on 2026-08-03 with the 14.3 and the 13.4 built: 258
  MB of files for the two, and a 133 MB database volume each. The per-project
  DDEV images — `ddev/ddev-webserver:v1.25.1-typo3-mcp-e-site-14-3-built` and
  its dbserver — report `UNIQUE SIZE` 0 B in `docker system df -v`: they are
  tags on layers every DDEV project on the machine already shares. So a covered
  line is about 260 MB, and every released one is about 780 MB.
- Both builds were six `ddev` commands on a warm Composer cache, 36 and 34
  seconds, from an empty directory to `site:list` and `language:domain:search`
  answering through `ddev exec`. That is the measurement `D-EVI-004` made of one
  installation, taken again per version.
- Starting a stopped one instead was 14 seconds, against the 34 the build takes
  and the database it would meet.
- The recording no longer runs on any of them. `D-DOC-012` moved the second root
  to the installation this repository writes below `.fixtures/`, and rejected a
  third — so the number of installed instances is a question about `scenarios/`
  alone, not about `documentation/server/tools/`.
- `typo3/cms-base-distribution` publishes no release above the newest stable:
  `v14.3.0` is its top tag, and the only thing tracking the development core is
  its `dev-main`. That core declares PHP `^8.5` where the containers here are
  pinned to 8.4. Read on 2026-08-03 from packagist and from
  `.checkouts/main/composer.json`.
- One PHP covers every released covered line. Each branch's own
  `Build/Scripts/runTests.sh` takes 8.4 — 12.4 accepts `8.1` to `8.5`, 13.4 and
  14.3 accept `8.2` to `8.5` — read in `.checkouts/` on 2026-08-03.

## Decided

- The version is the second argument of `environment:create`, not a command of
  its own. `create` already takes the environment id, the build is the same six
  commands with one constraint changed, and a second command would carry the
  whole of it a second time.
- Named no version it is the covered stable one, which is what a case that says
  nothing about a version is run on. That keeps
  `bin/cli environment:create E-SITE` meaning what it meant.
- One DDEV project name and one directory per version, both carrying the branch.
  The name is global to the machine, so one name for all of them is one
  installation for all of them — which is the state this entry is about.
- An installation that is there is started, never built again: `ddev start`
  where the containers are in any state but `running`, the pause DDEV puts an
  idle project into included. The build is minutes and the containers are
  seconds, and an environment is only worth keeping if asking for it again costs
  the seconds.
- `environment:status` is a row per covered version — which are installed here,
  which are missing, and which is not made at all — because "which of these do I
  have" is the whole of what that command is for.
- ~~The development line is not made here, and says so with the reason. It is a
  different build rather than a version argument: another package constraint,
  another stability, another PHP.~~ Reversed on 2026-08-03, see **Since then**.
  It is all three of those and it is made anyway.
- Rejected: making all of them by one command. Which covered lines a machine
  holds is a property of that machine and of what somebody is about to run, and
  `status` already names the ones that are missing with the command that makes
  each.

## Assumed

- That two installations are what the scenarios need, and the other released
  lines are made on the day a case asks for one. Nothing holds that: it is what
  the case files say today, and 260 MB per line is cheap enough that a session
  can be wrong about it for the price of a `create`.
- That the covered stable line stays the version a case with no version is run
  on. `branch()` reads that off `knowledge/versions.json`, so a new stable major
  moves it and the old installation stays behind under its own name.

## Wrong if

- A case is run on the wrong version because `create` was asked for none and the
  caller wanted another. The default is silent, and the directory name is the
  only place the version is visible.
- The installations are made once and never started again, so the `ddev start`
  path is exercised by nobody and the environments are rebuilt by hand anyway.
- The disk cost turns out to be the thing that stops somebody holding more than
  one — a machine where 260 MB per line is not the number, because the DDEV
  images stop being shared or the database grows with what a run writes into it.
- The development line's installation is never refreshed, so what a case run in
  it measures is a `main` from whenever somebody last built it. Nothing here
  dates it and `status` reports it like any other row.
- A case that turns on what the database does is run in an environment where no
  line has one. Every covered line is on sqlite since `c27f8bd`, and an
  installation on sqlite answers what a console question asks and says nothing
  about what MariaDB does under the same schema.

## Covered by

- `EnvironmentsTest::eachDriverPassesTheValuesItsOwnToolsTake`
- `EnvironmentsTest::aDatabaseNothingIsMadeOnIsRefusedWithTheOnesThereAre`
- `EnvironmentsTest::anInstallationOnASecondDatabaseIsItsOwnProjectAndItsOwnDirectory`
- `EnvironmentsTest::everyCoveredLineIsOneAnInstallationIsMadeOf`
- `EnvironmentsTest::aVersionNoInstallationIsMadeOfSaysWhyRatherThanNothing`
- `EnvironmentsTest::eachCoveredLineIsItsOwnProjectAndItsOwnDirectory`
- `EnvironmentsTest::anInstallationThatIsThereIsStartedRatherThanBuiltAgain`
- `EnvironmentsTest::theInstallationIsBuiltAtTheCoveredStableVersion`
- `EnvironmentsTest::theDevelopmentLineIsBuiltFromDevMainOnThePhpItsCoreDeclares`

## Since then

### 2026-08-03 — the development line is made after all, and its build found one thing reading could not

The **Decided** above declines the development line, and the todo behind it
carried the question rather than the refusal: does that line get an installation
of its own shape, or stay declined? The answer came from the person who queued
it — *für dev-main brauchen wir 8.5* — with the reason that settles the second
half too: these environments exist to develop this server and nothing else, so
what a daily-moving installation costs is a development cost and not somebody's
site.

So `branches()` is every covered line now, `refusal()` keeps only the version
nothing covers, and the difference the old entry called "a different build" is
three things carried by `constraint()`, `php()` and one step:

- **`dev-main`, at a dev stability.** `typo3/cms-base-distribution` still
  publishes no release above `v14.3.0`, so the development line is its
  `dev-main`, which requires `dev-main` of twenty-four core packages and
  declares no `minimum-stability` of its own. `--stability=dev` is on
  `create-project` alone: a root requirement's own constraint sets its stability
  flag, so the `typo3/cms-lowlevel:dev-main` beside it needs nothing.
- **PHP 8.5**, because `.checkouts/main/composer.json` declares `"php": "^8.5"`
  and a platform of `8.5.0`. DDEV 1.25.1 offers `5.6` through `8.5`. The
  released lines keep 8.4: what their environment is for is answering the way an
  installation of them answers, and their own `runTests.sh` runs 8.4.
- **The platform pin the distribution outgrew.** This is the one nothing here
  could have reasoned to, and the first build is where it surfaced: the
  distribution's `dev-main` pins `config.platform.php` to `8.2.0` on the same
  branch whose `typo3/cms-core: dev-main` requires `php ^8.5`. The container
  came up correctly at 8.5.3 and Composer resolved against 8.2.0 anyway, ending
  in *your php version (8.2.0; overridden via config.platform, actual: 8.5.3)
  does not satisfy that requirement* — twice, once for `cms-core` and once for
  `symfony/dependency-injection` under `cms-extbase`. So the build creates
  without installing and unsets the pin before anything resolves. It is unset
  rather than raised, because the PHP this environment should resolve against is
  the one its container runs.

- **sqlite, because `setup` does not finish on `main` against MariaDB.** The
  second build got past Composer and died on the installation step with *A
  database is required for the method:
  `Doctrine\DBAL\Platforms\MySQL\MySQLMetadataProvider::__construct`*.
  `SetupDatabaseService::getDatabaseList()` builds a connection with `dbname`
  unset on purpose — so a wrong database name can still be corrected — and then
  asks it for a schema manager; `doctrine/dbal` 4.4.4 reads `SELECT DATABASE()`
  there, gets null, and refuses. `SetupCommand::selectAndImportDatabase()` runs
  that for every driver but sqlite, before anything is written, so no option of
  the setup avoids it. Read on 2026-08-03 in `typo3/cms-install` at
  `af648f05bbc3` and `typo3/cms-core` at `300245e2`, against the DBAL that
  branch locks itself (`~4.4.3`, resolved 4.4.4). The same call is in `14.3`,
  where the DBAL is older and does not refuse.

That last one is why `D-EVI-004`'s measurement is taken by running rather than
by reading: the recipe that fails is shaped exactly like the recipe that works
until somebody runs it, and no file in this repository or in that package says
the two constraints disagree. It is also the first thing this line has bought —
a defect on the next major, found by building an installation of it, which is
the whole argument for holding one.

The sqlite driver is a workaround with a date on it, and it is the one part of
this that somebody may want reversed: an installation on sqlite answers what a
console question asks and says nothing about what MariaDB does under the same
schema. `Environments::DEVELOPMENT_DRIVER` is the whole of it, and the released
lines are untouched.

**Measured on 2026-08-03**, built from an empty directory by
`bin/cli environment:create E-SITE main`: 28 seconds for the seven steps on a
warm Composer cache, against the 34 a released line takes.
`TYPO3 CMS 15.0.0-dev (Application Context: Production) - PHP 8.5.3` is what the
console reports, which is both pins holding at once. 131 MB of files and no
database volume at all — sqlite is a file inside that number, so the development
line is half what a released one costs rather than the 260 MB the refusal priced
it at. `site:list` returns the created site, `language:domain:search` finds 159
label references and `configuration:show BE/debug` answers, which is what
`scenarios/readme.md` requires of an `E-SITE`.

### 2026-08-03 — the defect is a TYPO3 change on three branches, not a DBAL bump on one

The account above names the DBAL bump as what brought the failure and `main` as
where it lives, and the **Wrong if** below prices the sqlite driver as the
development line's workaround. All three have been read again and none of them
holds.

What put the refusing call there is Forge #110258, *Use Doctrine introspection
API for table and database names*, merged to `main`, `14.3` and `13.4` on
2026-08-02. It replaced `listDatabases()` — a plain `getListDatabasesSQL()`
query — with `introspectDatabaseNames()`, which reaches
`AbstractSchemaManager::createSchemaProvider()` and constructs
`MySQLMetadataProvider`, whose constructor runs `SELECT DATABASE()` and throws
`DatabaseRequired` on the null it always gets from a connection with no database
selected. The provider's own `getAllDatabaseNames()` reads
`information_schema.SCHEMATA` and needs no current database; only the
constructor does. `12.4` is on `doctrine/dbal ^3.9`, has no introspection API to
move to, and is unaffected; `13.4` and `14.3` lock the same `~4.4.3` as `main`,
so the DBAL version was never the difference between them.

The evidence that produced the wrong account is worth keeping, because it is a
trap this repository will meet again. `.checkouts/` was fetched before
2026-08-02, so `getDatabaseList()` read there shows `listDatabases()` on all
three branches — while the `typo3/cms-install` the environment had installed
already carried the new call. Two readings of one file that disagreed about the
date read as a version boundary. `bin/cli checkouts:update` is what dates them,
and a checkout is only evidence about the day it was fetched.

Why the 14.3 installation built anyway is the same date, from the other side: it
installs `^14.3`, and no release carries the backport — `typo3/cms-install`
v14.3.5 and v13.4.33 are both from 2026-07-14, read from packagist on
2026-08-03. So the next patch release of either line ships a `setup` that cannot
finish against MySQL or MariaDB, and this is not a next-major problem.

It is unreported and unfixed. Five differently worded Forge searches return
#110258 and nothing else, and `file:SetupDatabaseService.php after:2026-08-01`
in Gerrit is the three merged backports and no fourth change. The report is
written and waiting on somebody with an account, on
`todo/waiting/2026-08-03-104500-file-the-setup-defect-that-cannot-finish-on-main.md`.

The workaround the **Wrong if** watched is no longer one. `c27f8bd` removed
`Environments::DEVELOPMENT_DRIVER` and put every covered line on sqlite, for a
reason the defect did not buy: no database container, no volume named after the
project, and `rm -rf` is the whole of taking an environment away. What that
**Wrong if** was written to catch — an installation whose database is not the
one every other line runs — cannot happen now, and what replaced it is that no
line's database is MariaDB.

### 2026-08-04 — sqlite stays on every line, and the reversal is not a question any more

Put to the person who queued the card, with the defect and the driver priced
apart: the answer is that sqlite stays. So the second half of that card is gone
rather than deferred, and the **Wrong if** above is rewritten to the risk that
is actually carried — every covered line is on sqlite, and none of them says
what MariaDB does.

What settles it is that the two are no longer one question. The defect bought
sqlite on the development line for a day; `c27f8bd` bought it on all four for
reasons of its own — no database container, no volume, and a directory that is
removed by deleting it. Reversing now would be putting four lines back on
MariaDB against that decision rather than taking a workaround out, and `main`
would not install at all until the defect is fixed.

The report itself is unchanged and still unfiled. It needs an authenticated
Forge account, this repository holds credentials for none, and the card is
trimmed to that one act.

### 2026-08-04 — the database is the third argument, because sqlite alone answers nothing about a database

The answer above settled which database an environment gets by default and left
the **Wrong if** it replaced with: every covered line is on sqlite, so a case
turning on what a database server does has nowhere to be run. Put back the same
day, the answer was that the environments cover more than one — so `sqlite`
stops being one constant and becomes the default of four.

`bin/cli environment:create E-SITE <version> <database>` is what asks for
another, and the driver joins the version in the DDEV project name and the
directory for the reason the version is in them: one name for two installations
is one installation. Only a non-default driver adds a suffix, so every
environment that exists and every path `todo/reference/` names is untouched.
`environment:status` gains a row per database actually made, rather than four
rows of "missing" per version for something almost nobody asks for.

**The two tools disagree on every name, which is the whole of what the table
buys.** `ddev config --database` takes a `type:version`, refuses a bare type,
and does not check the version until `ddev start` — `mariadb:99.9` configures
cleanly and fails minutes later, measured against DDEV v1.25.1 on 2026-08-04.
`vendor/bin/typo3 setup --driver` takes a connection type out of
`SetupCommand::$connectionLabels`, which is not the DBAL driver it resolves to:
`mysqli` stays `mysqli`, `postgres` becomes `pdo_pgsql`, `sqlite` becomes
`pdo_sqlite`. A value passed where the other belongs is a build that installs a
hundred packages before it says so.

**The versions are the newest every covered line accepts**, intersected across
all four `Build/Scripts/runTests.sh` in `.checkouts/` on 2026-08-04: mariadb
10.4–11.8 above 12.4 and 10.3–11.4 there, mysql 8.0–8.4 on all four, postgres
10–18 above 12.4 and 10–16 there. So `mariadb:11.4`, `mysql:8.4` and
`postgres:16`, and it is 12.4 that decides each of them.

**The connection is one set of values for both services**, measured by building
a DDEV project on each and reading it back on 2026-08-04: host `db`, database
`db`, user `db`, password `db`, and only the port moves — 3306 and 5432. The
password is passed as an option rather than left to `TYPO3_DB_PASSWORD`, because
`SetupCommand` forces the password question even under `--no-interaction` where
neither is set, and `getFallbackValueEnvOrOption` reads the option first.

**Postgres is the one service database every covered line can be built on
today.** The defect above is MySQL's alone:
`PostgreSQLMetadataProvider::__construct` has an empty body where
`MySQLMetadataProvider::__construct` reads `SELECT DATABASE()`, read in
`doctrine/dbal` 4.4.3 on 2026-08-04. So mariadb and mysql are in the table and
cannot be built on `main`, `14.3` or `13.4` until the report above is fixed,
which `environment:create` says in its own words when a build dies there.

Nothing has been built on a service database yet. What is held is the values
each one is passed, over every driver, and that is what the tests above cover —
a build is a container start and a hundred packages, which no test here does.

### 2026-08-12 — the defect was filed and fixed by somebody else, and every driver is installable again

The report above waited on somebody with a Forge account, and it never needed
one. Garvin Hicking filed the defect as #110381 on 2026-08-05, out of
`bmack/tryout` #9 by Simon Praetorius rather than out of anything measured here.
Change 95117 and the two backports beside it were merged to `main`, `14.3` and
`13.4` on 2026-08-06, and they fix it where this repository's report said it
sits: `SetupDatabaseService` now names `information_schema` as the `dbname`,
which satisfies Doctrine's precondition and leaves what `getAllDatabaseNames()`
returns untouched. `typo3/cms-install` v14.3.6 and v13.4.34, both from
2026-08-11, carry it — read from packagist and from Gerrit on 2026-08-12.

So mariadb and mysql can be installed on every covered line again, and the two
places that said they could not are gone: the paragraph on
`Environments::DRIVERS` and the message `environment:create` printed when a
build on a service driver died. One sentence naming both issue numbers replaces
them, because a driver that was unusable for four days is worth a line rather
than a paragraph.

The default does not move. sqlite is what every environment is built on, for the
reasons `c27f8bd` had rather than for this defect, and nothing has been built on
a service database yet: the fix is read off the merged change and the releases,
not off a build.
