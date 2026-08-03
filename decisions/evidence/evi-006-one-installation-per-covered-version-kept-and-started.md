---
id: D-EVI-006
date: 2026-08-03
status: open
---

# D-EVI-006 — One installation per covered version, kept and started

**`bin/cli environment:create E-SITE <version>` makes one installation per
covered version, each its own DDEV project, and starts one that is already
there rather than building it again.**

One installation runs one version, and there was one installation. So every
case naming `E-SITE` ran on the covered stable major, and what a client on
another covered line would be answered was shown by nothing.

## Evidence

- Twenty contract cases name `E-SITE`, and one of them names a version:
  `SITE-02` is `E-SITE` on the previous major. No case names 12.4 or the
  development line, so the demand the scenarios carry today is two
  installations rather than four.
- What one costs, measured on 2026-08-03 with the 14.3 and the 13.4 built: 258
  MB of files for the two, and a 133 MB database volume each. The per-project
  DDEV images — `ddev/ddev-webserver:v1.25.1-typo3-mcp-e-site-14-3-built` and
  its dbserver — report `UNIQUE SIZE` 0 B in `docker system df -v`: they are
  tags on layers every DDEV project on the machine already shares. So a covered
  line is about 260 MB, and every released one is about 780 MB.
- Both builds were six `ddev` commands on a warm Composer cache, 36 and 34
  seconds, from an empty directory to `site:list` and `language:domain:search`
  answering through `ddev exec`. That is the measurement `D-EVI-004` made of
  one installation, taken again per version.
- Starting a stopped one instead was 14 seconds, against the 34 the build takes
  and the database it would meet.
- The recording no longer runs on any of them. `D-DOC-012` moved the second
  root to the installation this repository writes below `.fixtures/`, and
  rejected a third — so the number of installed instances is a question about
  `scenarios/` alone, not about `documentation/clients/tools/`.
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
  nothing about a version is run on. That keeps `bin/cli environment:create
  E-SITE` meaning what it meant.
- One DDEV project name and one directory per version, both carrying the
  branch. The name is global to the machine, so one name for all of them is one
  installation for all of them — which is the state this entry is about.
- An installation that is there is started, never built again: `ddev start`
  where the containers are in any state but `running`, the pause DDEV puts an
  idle project into included. The build is minutes and the containers are
  seconds, and an environment is only worth keeping if asking for it again
  costs the seconds.
- `environment:status` is a row per covered version — which are installed here,
  which are missing, and which is not made at all — because "which of these do
  I have" is the whole of what that command is for.
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
  on. `branch()` reads that off `knowledge/versions.json`, so a new stable
  major moves it and the old installation stays behind under its own name.

## Wrong if

- A case is run on the wrong version because `create` was asked for none and
  the caller wanted another. The default is silent, and the directory name is
  the only place the version is visible.
- The installations are made once and never started again, so the `ddev start`
  path is exercised by nobody and the environments are rebuilt by hand anyway.
- The disk cost turns out to be the thing that stops somebody holding more than
  one — a machine where 260 MB per line is not the number, because the DDEV
  images stop being shared or the database grows with what a run writes into
  it.
- The development line's installation is never refreshed, so what a case run in
  it measures is a `main` from whenever somebody last built it. Nothing here
  dates it and `status` reports it like any other row.
- The sqlite workaround outlives the defect that bought it, so the development
  line goes on being the one installation whose database is not the one every
  other line runs — and a case that turns on the database is answered from it
  as if it were.

## Covered by

- `EnvironmentsTest::everyCoveredLineIsOneAnInstallationIsMadeOf`
- `EnvironmentsTest::aVersionNoInstallationIsMadeOfSaysWhyRatherThanNothing`
- `EnvironmentsTest::eachCoveredLineIsItsOwnProjectAndItsOwnDirectory`
- `EnvironmentsTest::anInstallationThatIsThereIsStartedRatherThanBuiltAgain`
- `EnvironmentsTest::theInstallationIsBuiltAtTheCoveredStableVersion`
- `EnvironmentsTest::theDevelopmentLineIsBuiltFromDevMainOnThePhpItsCoreDeclares`

## Since then

### 2026-08-03 — the development line is made after all, and its build found one thing reading could not

The **Decided** above declines the development line, and the todo behind it
carried the question rather than the refusal: does that line get an
installation of its own shape, or stay declined? The answer came from the
person who queued it — *für dev-main brauchen wir 8.5* — with the reason that
settles the second half too: these environments exist to develop this server
and nothing else, so what a daily-moving installation costs is a development
cost and not somebody's site.

So `branches()` is every covered line now, `refusal()` keeps only the version
nothing covers, and the difference the old entry called "a different build" is
three things carried by `constraint()`, `php()` and one step:

- **`dev-main`, at a dev stability.** `typo3/cms-base-distribution` still
  publishes no release above `v14.3.0`, so the development line is its
  `dev-main`, which requires `dev-main` of twenty-four core packages and
  declares no `minimum-stability` of its own. `--stability=dev` is on
  `create-project` alone: a root requirement's own constraint sets its
  stability flag, so the `typo3/cms-lowlevel:dev-main` beside it needs nothing.
- **PHP 8.5**, because `.checkouts/main/composer.json` declares `"php": "^8.5"`
  and a platform of `8.5.0`. DDEV 1.25.1 offers `5.6` through `8.5`. The
  released lines keep 8.4: what their environment is for is answering the way
  an installation of them answers, and their own `runTests.sh` runs 8.4.
- **The platform pin the distribution outgrew.** This is the one nothing here
  could have reasoned to, and the first build is where it surfaced: the
  distribution's `dev-main` pins `config.platform.php` to `8.2.0` on the same
  branch whose `typo3/cms-core: dev-main` requires `php ^8.5`. The container
  came up correctly at 8.5.3 and Composer resolved against 8.2.0 anyway,
  ending in *your php version (8.2.0; overridden via config.platform, actual:
  8.5.3) does not satisfy that requirement* — twice, once for `cms-core` and
  once for `symfony/dependency-injection` under `cms-extbase`. So the build
  creates without installing and unsets the pin before anything resolves. It
  is unset rather than raised, because the PHP this environment should resolve
  against is the one its container runs.

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
warm Composer cache, against the 34 a released line takes. `TYPO3 CMS
15.0.0-dev (Application Context: Production) - PHP 8.5.3` is what the console
reports, which is both pins holding at once. 131 MB of files and no database
volume at all — sqlite is a file inside that number, so the development line is
half what a released one costs rather than the 260 MB the refusal priced it at.
`site:list` returns the created site, `language:domain:search` finds 159 label
references and `configuration:show BE/debug` answers, which is what
`scenarios/readme.md` requires of an `E-SITE`.
