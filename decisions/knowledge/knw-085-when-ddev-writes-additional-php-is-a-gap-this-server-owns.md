---
id: D-KNW-085
date: 2026-08-18
status: confirmed
---

# D-KNW-085 — When DDEV writes additional.php is a gap this server owns

**When DDEV's settings management writes `config/system/additional.php` is
inside this server's boundary and missing from it, so the feedback is queued.**

The corpus states what that file contains and who owns it, and the one sentence
touching the timing says it is rewritten on every start. A clone is booted in
the only order a clone allows — the environment before the dependencies — and
the session that does gets no file at all, an HTTP 500 on every request, and an
exception naming the trusted hosts pattern rather than the file that carries it.

## Evidence

- The subject is delivered and the timing is not in it.
  `bin/cli hints:probe "DDEV additional.php trustedHostsPattern first start fresh clone"`
  reaches `project-configuration-files` at `appliesTo(37) + text(335)` and
  `installation-boot` at `appliesTo(11) + text(506)`. The reporting session
  fetched the hint by id, so nothing was missed on the way to it.
- The four statements of `project-configuration-files` name the sections DDEV
  generates, the `#ddev-generated` marker, both ways of taking the file over and
  the `config/system/.gitignore` interaction. None of them says when the file
  appears.
- `knowledge/task-intents.json` states the opposite for the case reported. The
  `installation-operations` checklist carries "While the #ddev-generated marker
  line is in that file it is rewritten on every start", which on a clone with no
  `vendor/` promises a file the start does not write.
- The failure names none of that. `installation-boot` carries exception
  1396795884 as `VerifyHostHeader` against `SYS/trustedHostsPattern`, which
  reads as a site configuration problem; here the pattern is missing because the
  file supplying it is.
- The install reports success either way. `typo3 setup` writes its own
  `config/system/settings.php` from the environment variables, so only the web
  requests fail — the feedback reports the console succeeding on both rebuilds.
- The claim contradicts a reading already recorded here.
  [`D-KNW-049`](knw-049-what-ddev-writes-into-the-settings-is-named-in-full-and-so-is-what-it-cannot-configure.md)'s
  **Confirmed on 2026-08-03** read `createTypo3SettingsFile` and
  `writeTypo3SettingsFile` at DDEV v1.25.1 and concluded that the only thing
  stopping the file being written at all is `disable_settings_management`.
  Whatever the reporting session met was not on the path that reading covered.
- DDEV is on this machine and its source is not. `ddev version` answers v1.25.1;
  the Go package is on GitHub, so establishing the condition is an open-web
  read.

## Decided

- Step 1a of the ladder, and queued rather than closed on the spot. What lands
  is a statement about DDEV's generator, and this run has read nothing but this
  repository.
- The reading comes before the wording. The checklist sentence is wrong for one
  case and right for the rest, so what replaces it depends on the condition DDEV
  actually applies — rewritten from the report, it would carry the reporting
  session's hypothesis with the corpus's authority.
- The statement goes on `project-configuration-files`, beside the sections DDEV
  writes, and the `installation-operations` checklist item is corrected by the
  same commit. Two places state what DDEV does with that file, and repairing one
  leaves the other saying "on every start".
- Priority `normal`, from one session. What lifts it off `low` is that the
  corpus asserts the opposite rather than staying silent, and that the cost is a
  500 diagnosed against TYPO3's exception while the cause is the environment's
  write order.
- Not step 5. The report satisfies no **Wrong if** of `D-KNW-049` — those are
  about the `DB` section, a generator reading the driver, and a session that
  never asked about `config/system/`. What it disputes is a sentence in that
  entry's confirmation, and it is recorded there.
- Nothing is owed to `skills/typo3-development-installation`, which is what the
  feedback also asks for. That skill keeps routing and workflow and holds no
  environment defaults, and its proving step is what surfaced the failure by
  asking that the sequence run unattended from a clone. What is missing is the
  answer the session goes looking for at that point, and it is owned by the
  hint.

## Assumed

- That the reporting session's account of its own failure holds: the file was
  absent rather than present and unread. It names the absence from two rebuilds
  and reports `config/system/` coming back holding only `settings.php` and the
  `.gitkeep`.
- That the condition belongs to DDEV rather than to that project. One project
  reported it, at DDEV v1.25.1.

## Wrong if

- The reading finds no condition beyond `disable_settings_management`, and what
  the session met was its own project's history — a `.ddev/config.yaml` written
  between the two starts, a docroot that did not exist yet. The corpus is then
  right and there is nothing to state.
- The condition turns out to be the document root rather than an installation.
  "Recognises an installation" is the reporting session's phrase for what it
  observed, and it had already disproved one hypothesis of its own.
- A restart is not the remedy in general. The file appears at the next start
  only where the dependencies are installed by then, so a sequence that starts
  twice and installs nothing between still answers 1396795884.
- The next report arrives from a session that never asked what configures the
  installation. The statement would then be owed where a clone is booted, which
  is `installation-boot`, rather than where the file is owned.

## Covered by

- `HintsTest::theDdevSettingsAnswerSaysWhenThatFileIsWritten`

## Confirmed on 2026-08-18

Read in DDEV v1.25.1's own source, which is what the first two **Wrong if**
asked for: `pkg/ddevapp/typo3.go` and `pkg/ddevapp/apptypes.go` at the tag. The
condition is a detection and it belongs to DDEV rather than to the reporting
project. `CreateSettingsFile` calls `SetApptypeSettingsPaths` before the
generator, `setTypo3SiteSettingsPaths` looks for an installed TYPO3 — the
`Typo3Version.php` below the Composer vendor directory for a Composer
installation, `docroot/typo3` for a legacy one — and where it finds neither it
sets both paths to files at the project root. `createTypo3SettingsFile` returns
on its first line where the file's directory is that root, before the warning it
would otherwise print, so the start says nothing at all. `CreateGitIgnore` is
skipped by the same test in `CreateSettingsFile`, which is why the reporting
session found `config/system/` holding only what it had committed.

So the reporting session's account holds and the phrase it used for the trigger
was accurate. It is the installed core rather than the document root, which is
the third **Wrong if** settled too: the hypothesis that session disproved for
itself — that `config/system/` existing is what triggers the write — is
disproved in the source.

One thing the statement says differently from the feedback's own suggestion. The
report asks for the clone to be named as a second trigger for taking the file
over, beside the SQLite and `omit_containers` case. Those two are not the same
kind: the database-less installation is a collision that every start repeats, so
the file has to be taken over there, while the clone is an ordering that a
second start ends for good. So the statement names starting again as the way out
and the committed project-owned file as the other, rather than presenting the
takeover as the remedy.

What the reading added beyond the report is the hooks. Both `CreateSettingsFile`
calls in `Start()` run before `ProcessHooks("post-start")`, so the install hook
`installation-operations` already discusses does not produce the file in the
start that ran it — which is exactly the shape of setup that intent is briefing.

`R-KNW-071` holds both places, and `bin/cli hints:probe` on the reporting
session's own subject — "DDEV additional.php trustedHostsPattern first start
fresh clone" — reaches `project-configuration-files` first at
`appliesTo(37) + text(366)`, against the `text(335)` this entry recorded before
the statement landed.
