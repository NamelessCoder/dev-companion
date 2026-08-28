---
id: D-KNW-046
title: 'The non-interactive install path is a subject this server owns'
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::anUnattendedInstallIsAnsweredWithWhatTheCommandRefuses
---

# D-KNW-046 — The non-interactive install path is a subject this server owns

**What `typo3 setup` accepts and what it refuses is inside this server's
boundary and missing from it, so the feedback is queued.**

The corpus already gives the credential half of the answer and the sitepackage
half. What it never says is the mechanics an unattended installer stands on: the
driver name the option takes against the one that lands in `settings.php`, the
abort on a database that already has tables, and why the two site options are
inert in Composer mode. A session writing an install script is told what to
report afterwards and left to find out what the command takes.

## Evidence

- The miss reproduces on the server as it is now. `typo3_task_guide` with the
  feedback's own task recognises the intent — *Setting an installation up* — and
  its checklist carries the four credential lines and the `--create-site` caveat
  for a sitepackage, and no line about drivers, environment variables or a
  re-run. `typo3_hint_lookup` with the feedback's query matches no hint at all
  and falls back to the id index. `bin/cli hints:probe` on the same query
  reaches `sitepackage-initial-content`, `datahandler-seeding`,
  `environment-runtime-readers` and `fal-storages-drivers`, none of which is
  about the setup command.
- Half of point 3 is written and does not reach an install task.
  `sitepackage-initial-content` says, `since: 14`, that a package shipping an
  initialisation file counts as a distribution, that setup then skips its own
  site creation, and that the two options exclude each other. It is titled and
  worded for an extension author shipping content, and `installation-setup`
  reaches it only as a pointer in its `tools` list. What it does not say is what
  the feedback paid for: the consequence is that no site configuration is
  written, so an installation seeded this way cannot be told its own URL.
- Every claim the feedback makes about TYPO3 holds at 14.3, read in
  `.checkouts/14.3`. `SetupCommand::$connectionLabels` keys the accepted
  connection types
  `mysqli, mysqliSocket, pdoMysql, pdoMysqlSocket, postgres, sqlite`, and
  `SetupDatabaseService` line 678 sets `sqliteManualConfigurationOptions` to
  `driver => pdo_sqlite` — so the value written to `settings.php` is not a value
  the option accepts. The same service calculates the path itself,
  `var/sqlite/cms-<hex>.sqlite`. The two options throw `1775034289` together,
  warn *have no effect, when distributions are already active* where
  `$distributions['active']` is not empty, and `getAvailableDistributions()`
  classifies by `PackageManager::isPackageActive()`. The database validator
  throws `1669747200` where `tables !== 0`.
- The order the feedback verified is one command longer than it needs to be.
  `SetupCommand` calls `setupExtensions()` as its last step, which is what
  `sitepackage-initial-content` already states, so `typo3 extension:setup` after
  a successful `typo3 setup` re-runs a step that has run. It is not wrong and
  the corpus says so; only the account in the feedback reads as though it were
  required.

## Decided

- Step 1a for the driver names, the environment variables and the re-run, and
  step 2 for the two site options. Both go into one entry, because a caller
  writing an install script asks them in one breath.
- Queued rather than closed on the spot. The statement is owed the reading on
  12.4, 13.4 and `main` as well, and the judging run has read 14.3 and this
  repository.
- Not step 5. `doesNotCover` excludes operating an installation — servers,
  deployment, backups. Creating one is what `installation-setup` and
  `installation-upgrade` already answer for, and this is the same subject one
  question deeper.
- `normal` rather than `low`. The domain around it is the widest thing on the
  board: `bin/cli feedback:list` holds 29 open feedback, and bringing a local
  instance up is what six of them are about, from two directories, plus two in
  `feedback/archive/`. The specific gap is one session's, and the domain it sits
  in is not.
- The three cards its siblings carry stay where they are. `2026-08-03-162836` is
  the impexp base rewrite and `2026-08-03-162858` is DDEV's settings management,
  which are different subjects with different evidence, and both are in hand
  elsewhere.

## Assumed

- The caller is in Composer mode. Every statement the feedback makes about the
  two site options turns on it, and a classic-mode installation is not what this
  server's callers work in.
- What holds at 14.3 holds back to 12.4 for the driver names and the re-run
  guard. That is what makes it one entry rather than four version-bound
  sentences, and it is the first thing the todo checks.

## Wrong if

- The reading finds the connection labels or the `pdo_sqlite` persistence differ
  across 12.4, 13.4 and 14.3. Then it is not one statement but a version
  boundary, and the entry carries `since`/`until` rather than a flat claim.
- A statement about the setup command turns out to be the install guide in the
  official manual restated. Then the subject belongs behind
  `typo3_documentation_lookup`, and `installation-setup` needs that pointer
  instead of a hint of its own.
- The entry lands and a later install session still reaches nothing. Then the
  gap was never the knowledge but where an install task looks, and the fix is on
  `installation-setup` rather than in `knowledge/hints/`.

## Confirmed on 2026-08-03

The mechanics are one flat statement across all four checkouts — the same six
connection types, the same driver options, the same calculated path and the same
validator — so the first **Wrong if** does not hold and the assumption is
confirmed.

The two site options are the half that moved, which the entry did not expect:
the older majors have neither the option nor the check behind it, so the site is
written there whatever else is installed. That reading also bound one statement
of a neighbouring hint that was stated flat and is false on both LTS branches.

One imprecision stays: the option arrived inside the covered major, and the
corpus binds by major, so expressing that would be a change to the model rather
than to this entry.

## Since then

Two sessions were judged against this entry. The first reached both hints from
its own task and called them correct and useful, so the placement is not what
cost it anything — what it paid for is one question further out, which
`D-KNW-094` carries. The second reached the hint as this entry intended, and the
statement stops one file short of what the command writes: a page object that
outranks the site's sets, which is `D-KNW-116`.
