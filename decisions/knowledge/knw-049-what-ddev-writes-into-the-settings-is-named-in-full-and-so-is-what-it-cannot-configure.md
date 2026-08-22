---
id: D-KNW-049
date: 2026-08-03
status: confirmed
---

# D-KNW-049 — What DDEV writes into the settings is named in full, and so is what it cannot configure

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
  [`D-KNW-010`](knw-010-what-the-core-reads-from-the-environment-is-a-gap-this-server-owns.md)
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

## Covered by

- `HintsTest::theDdevSettingsAnswerNamesEverySectionItGeneratesAndTheDatabaseItAssumes`

## Since then

On 2026-08-04, the generated file was read in a repository of the other shape.
`/home/benji/projects/syntax` is an extension whose own `composer.json` is the
Composer root, with TYPO3 14.3 below `.build/vendor` and the docroot at
`.build/public`, and DDEV writes the same `#ddev-generated`
`config/system/additional.php` there — the file sits at the repository root,
because `typo3/cms-composer-installers` keeps the application directory at the
Composer root whatever the web directory is (`Plugin/Config.php:37`, and the
warning at 222 that no other value is supported).

## Since then

On 2026-08-18, a session disputed one sentence of the reading above.
`feedback/2026-08-17-205850` reports that DDEV writes the file only once it
recognises an installation, so the first start of a clone with no `vendor/`
writes nothing and the site answers 1396795884 until the project is started
again after the dependencies are installed. That is a second thing stopping the
file being written, where this entry's confirmation found only
`disable_settings_management`. The reading was of the writer rather than of what
calls it, and nothing here settles which of the two is right, so the statement
of this entry stands and
[`D-KNW-085`](knw-085-when-ddev-writes-additional-php-is-a-gap-this-server-owns.md)
carries the question.

## Confirmed on 2026-08-03

Read in DDEV v1.25.1 itself, which is what the first and third **Wrong if**
asked for: the template `pkg/ddevapp/typo3/AdditionalConfiguration.php` and the
`createTypo3SettingsFile` and `writeTypo3SettingsFile` beside it, at the tag and
in the binary installed on this machine. The `DB` block carries no condition.
`DBHostname` is the literal `db`, `DBDriver` is `mysqli` unless the project's
database type is Postgres, and `GetInternalPort` answers the constant 3306 or
5432. Nothing on that path reads `omit_containers` or the driver the
installation was set up with, and the only thing that stops the file being
written at all is `disable_settings_management`. So DDEV does write the `DB`
section for a database-less project, as the reporting session described, and the
one generated file read for the entry was the template rather than a sample of
it.

One thing the rewrite states differently from `R-KNW-060` as it was queued. Both
ways out end the collision — a file without the marker and a project with
`disable_settings_management` are both left alone — so what the statement says
is which of the two keeps `GFX`, `MAIL` and `SYS` as DDEV already wrote them,
rather than that one of them is the only one left. The requirement was corrected
to that in the same commit.

## Since then

Both lifecycle findings have landed, and neither of them in `knowledge/hints/`.
`D-GUI-008` gave work that operates an installation a change type of its own,
and the `installation-operations` checklist it wrote states the first one whole:
`fail_on_hook_fail` defaults to false, so a post-start hook that installs the
instance can fail while `ddev start` reports success, and an install hook
belongs behind `fail_on_hook_fail: true`. `D-ANS-044` is not where it went. That
entry counted this feedback as evidence that the subject recurs, and the field
it built reports the hooks an environment declares, which says nothing about one
that fails. The default was re-read at DDEV v1.25.1: its documentation states it
on the configuration page, and the commented block DDEV writes into a generated
`config.yaml` carries `# fail_on_hook_fail: False`.

The second one is in `skills/typo3-development-installation/SKILL.md`, as an
observable rather than as DDEV's behaviour: step 2 has the environment
configuration read back after a command that rewrites it, to see what was set by
hand and is gone. That is all it can be, because the finding is not confirmed.
DDEV's own command documentation says the opposite of the report —
"`ddev config` will not change configuration that already exists in your
`.ddev/config.yaml`" — and what the reporting session saw is more likely the
file being written back out of DDEV's own structure, comments and defaults
included. Nothing in `knowledge/` asserts it either way, so nothing here rests
on which of the two it is.

No test names that checklist item.
`HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief` holds the
fork and two of the six items rather than the statement, and coverage for one
more of them is `D-GUI-008`'s to add.

So nothing is owed beyond what those two carry, and `feedback/2026-08-03-162858`
is archived by this commit. The reporting session's own task, re-run through
`bin/typo3-dev-companion` on 2026-08-03 — "run a local TYPO3 14.3.5 development
instance under DDEV for an extension, on SQLite with no database container" —
reaches `installation-operations` weakly, so the brief keeps the patch skeleton
and prints that intent's six items under their condition, the
`fail_on_hook_fail` one included. `bin/cli hints:probe` on the feedback's query
now reaches `project-configuration-files` at `appliesTo(47) + text(808)`,
against the `appliesTo(32) + text(467)` this entry recorded before the rewrite.

So the layout does not decide whether the file appears, and a card that claimed
it did was wrong when it was worked off.

That entry has since been read against DDEV v1.25.1's own source and the
reporting session was right. `disable_settings_management` is what stops the
file being written for a project DDEV recognises; `setTypo3SiteSettingsPaths` is
what decides whether it recognises one at all, and where it does not,
`createTypo3SettingsFile` returns before anything is written. So the sentence
above is the one that was too narrow, and the reading it came out of stopped one
call short of the caller. The statement of this entry is untouched by that: what
DDEV generates and what its generator cannot configure is what it settles, and
when it generates it is `D-KNW-085`'s.
