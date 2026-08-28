---
id: D-KNW-049
title: 'What DDEV writes into the settings is named in full'
date: 2026-08-03
status: confirmed
coveredBy:
  - HintsTest::theDdevSettingsAnswerNamesEverySectionItGenerates
---

# D-KNW-049 — What DDEV writes into the settings is named in full

**What DDEV's settings management writes into `config/system/additional.php` is
stated in full, and so is the installation its generator cannot configure.**

The corpus names one of the four sections it generates and offers disabling the
generation as a clean alternative. Those are the same sentence read twice: a
caller told that DDEV supplies the database settings writes the database
settings back and loses the other three, and the one that breaks the
installation immediately is `SYS`. So `feedback/2026-08-03-162858` is step 1a of
the ladder, on a hint that already exists, and it is queued as
[`R-KNW-060`](../../requirements/knowledge/knw-060-the-project-configuration-answer-names-what-ddev-writes-and-what-it-cannot-configure.md).

## Evidence

- The subject is delivered. `typo3_task_guide` and `typo3_hint_lookup`, called
  through `bin/typo3-dev-companion` from this repository on 2026-08-03 with the
  feedback's own task, both return `project-configuration-files` and quote its
  four statements whole. `bin/cli hints:probe` on the feedback's query reaches
  the same hint at `appliesTo(32) + text(467)`, and on "DDEV settings management
  additional.php ddev-generated marker" at `appliesTo(18) + text(534)`.
- What those statements say of the file is "DDEV writes its database settings to
  config/system/additional.php", and of the way out "Remove that marker to take
  the file over, or disable DDEV settings management". The fourth adds
  "Disabling DDEV settings management is the other clean choice when the
  project-owned file handles the local environment too".
- What is generated is four sections and not one. The `#ddev-generated` file at
  `/home/benji/projects/site-new/config/system/additional.php`, written by DDEV
  v1.25.1, merges under `getenv('IS_DDEV_PROJECT')` with
  `array_replace_recursive`: `DB` (`driver` `mysqli`, `host` `db`, `dbname`,
  `user` and `password` `db`, port 3306), `GFX` (ImageMagick at `/usr/bin/`),
  `MAIL` (smtp to `localhost:1025`), and `SYS` with `trustedHostsPattern`,
  `devIPmask` and
  `displayErrors`.
- `SYS` is what the route the corpus offers costs. The feedback reports
  `UnexpectedValueException` 1396795884 — the host header not matching the
  trusted hosts pattern — as the immediate symptom of taking the file over and
  writing only what the corpus names.
- The reported project is on this machine and matches its account.
  `/home/benji/projects/ext-guidedtour/.ddev/config.yaml` carries `type: typo3`
  and `omit_containers: [db]`, its `config/system/settings.php` carries
  `'driver' => 'pdo_sqlite'`, and its `additional.php` is the generated file
  with the marker line and the `DB` section removed by hand.
- The other half is not readable here. Whether DDEV writes that `DB` section
  when its database container is omitted is a property of DDEV's generator; this
  run read one generated file and started nothing.
- The boundary was already read this way.
  [`D-KNW-010`](knw-010-what-the-core-reads-from-the-environment-is-a-subject-this-server-owns.md)
  reads `doesNotCover`'s "Running an installation: server and container setup,
  deployment, backups" as excluding the operating of an installation rather than
  what its configuration files contain. The four DDEV statements sit in
  `project-configuration-files` on that reading, and
  [`R-KNW-032`](../../requirements/knowledge/knw-032-project-configuration-states-who-owns-which-file.md)
  holds them.

## Decided

- Step 1a, and queued rather than closed. The hint that would carry both facts
  exists and is reached, so the repair is a rewrite of statements in
  `knowledge/hints/project.json` and not a new subject.
- Not step 4 on its own. The wording that offers disabling as a clean choice is
  wrong because of the fact it is missing, and the fact has to be established
  before the sentence can be written.
- Not closed on the spot, because what DDEV generates for a project with no
  database container is DDEV's behaviour and this run has read one file it
  wrote. `judging.md` puts a lookup outside this repository beyond the spot.
- Priority `normal`, from one report. What raises it off `low` is that the
  corpus does not merely omit the answer: it recommends the route the session
  took, and the cost of that route is a debugging cycle that ends in an
  exception naming none of it.
- The two DDEV lifecycle findings are left out of the requirement.
  `fail_on_hook_fail` defaulting to false and a `ddev config` run rewriting
  `config.yaml` are container setup, which `doesNotCover` excludes, and neither
  says anything about what a TYPO3 configuration file contains.
- The feedback stays open. The card that asked for this judgement is deleted by
  the same commit, and the queued todo is what serves it until the rewrite
  lands.

## Assumed

- That the file read in `site-new` is what DDEV writes for every project of type
  `typo3` in Composer mode. The template belongs to DDEV rather than to the
  project, and it was read at v1.25.1 in one project.
- That the reported failure was the `SYS` half. The feedback names exception
  1396795884, and nothing else records that run.

## Wrong if

- The todo establishes that DDEV omits the `DB` section where the database
  container is omitted. Then only the four-sections half of `R-KNW-060` is owed,
  and the collision it describes is a mistake of the reporting session.
- A session reads the rewritten statement and disables settings management for a
  database-less installation anyway. That is step 4 and a rewrite, not a gap.
- DDEV ships a generator that reads the installation's own driver. The statement
  would then carry a version boundary rather than a limit.
- The next report of this comes from a session that never asked about
  `config/system/`. The answer would have to arrive from the question of what
  the environment declares, which is the route `feedback/2026-08-03-154501`
  describes.

## Confirmed on 2026-08-03

Read in DDEV itself, which is what the first and third **Wrong if** asked for:
the template and the two functions beside it, at the tag and in the binary
installed here. The database block carries no condition, and nothing on that
path reads the container list or the driver the installation was set up with. So
DDEV does write it for a database-less project, as the session described, and
the one generated file read for the entry was the template rather than a sample.

One thing the rewrite states differently: both ways out end the collision, so
what the statement says is which of the two keeps the rest of the file as DDEV
wrote it.

## Since then

A session disputed one sentence of the reading above: DDEV writes the file only
once it recognises an installation, so the first start of a clone with no
dependencies writes nothing. That is a second thing stopping the write, where
this confirmation found one — the reading was of the writer rather than of what
calls it.

It has since been read against DDEV's own source and the session was right: what
this entry found is what stops the write for a project DDEV recognises, and
whether it recognises one at all is decided a call earlier. `D-KNW-085` carries
that. What this entry settles is untouched: what DDEV generates, and what its
generator cannot configure.

## Since then

Two readings held this entry and changed nothing in it. The two lifecycle
findings the fifth **Decided** bullet left out landed, and neither in the hints:
one is a checklist item re-read at the installed DDEV, and one is written as an
observable rather than as DDEV's behaviour, because DDEV's own documentation
says the opposite of the report.

And the generated file was read in a repository of the other shape — an
extension whose own manifest is the Composer root — where DDEV writes the same
file at the repository root. So the layout does not decide whether it appears,
and a card that claimed it did was wrong when it was worked off.
