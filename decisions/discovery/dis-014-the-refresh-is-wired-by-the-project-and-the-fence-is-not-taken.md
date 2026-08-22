---
id: D-DIS-014
title: The refresh is wired by the project, and the fence is not taken
date: 2026-08-08
status: open
coveredBy:
  - InstallerRecordTest::updateSaysSoWhereNothingIsInstalledAtAll
  - InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState
---

# D-DIS-014 — The refresh is wired by the project, and the fence is not taken

**Of Laravel Boost's three ways of keeping a published copy current, this
package documents the Composer hook, already has the stale-name sweep, and does
not take the marker fence.**

`R-DIS-025` says a publication that went stale says so, and stops there:
somebody has to run `update`. Boost writes guidance into the user's project too,
so its three answers to the same problem were read before one was invented here.

## Evidence

- Boost's `GuidelineWriter` wraps what it writes in
  `<laravel-boost-guidelines>`, finds the block again with
  `/<laravel-boost-guidelines>.*?<\/laravel-boost-guidelines>/s`, and replaces
  it or appends one, under a retried file lock. What it fences is prose written
  into `CLAUDE.md` and `AGENTS.md` — markdown the user writes in as well.
- What this installer shares with a user is `.mcp.json`, `.codex/config.toml`
  and eleven more: JSON and TOML, merged by key and by section, keeping every
  field it does not own — `carriedOver()` and `rewrittenTomlSection()`,
  `D-AUD-005` and `D-AUD-006`. The skills go into a directory per skill,
  replaced whole, beside skills the project wrote itself.
- Boost's `SkillWriter::sync()` takes the names the caller tracked, `array_diff`
  against what it publishes now, and removes the rest.
  `Installer::publishSkills()` is that against `state.json`, and `outdated()`
  says it before the update runs.
- Boost does not write the Composer hook. Neither its own `composer.json`, its
  `InstallCommand` nor its `Support\Composer` touches an application's scripts;
  the Laravel documentation tells the reader to add
  `"post-update-cmd": ["@php artisan boost:update --ansi"]` themselves, and
  `UpdateCommand::runningAsComposerScript()` only keeps it from prompting there.
- Measured in a fixture project on 2026-08-08: Composer pushes the declared
  `bin-dir` onto `PATH` before running a script, so a bare `probe` resolved from
  `.build/bin`; the script ran in the project root; and exiting 1 ended the run
  with
  `Script probe handling the post-update-cmd event returned with error code 1`
  and Composer's own exit code 1.

## Decided

- The hook is documented and never written. `R-DIS-011` makes writing into a
  project an explicit `install`, and `composer.json` is the file that decides
  what the project consists of; a dev dependency that edits it during an update
  is deciding for the project what runs on every future one.
- `update` in a project with nothing installed succeeds and says so. It used to
  exit 1, which is right for the person who typed it and fatal for the hook: the
  record ignores itself (`R-DIS-024`), so it is in nobody's checkout, and the
  first colleague to run `composer update` would have it fail over a dev tool
  they never set up.
- The fence is not taken, because there is nothing to fence. Where this package
  shares a file the format has keys, and merging by them keeps more than a text
  block would; where it owns the bytes it owns a whole directory.
- The sweep needs nothing. It was read as the gap this package most clearly had,
  and it is in `publishSkills()` and held by
  `InstallerTest::codexUpdateRemovesSkillsTrackedByThePreviousCentralState`.
- Against the hook covering the fresh clone. `post-update-cmd` fires on `update`
  and on an `install` with no lock file, so a colleague installing from the lock
  runs nothing — that case stays the notice's, at the next server start.

## Assumed

- That a project's `composer.json` is the project's to edit, and that a line
  somebody added themselves is one they will read when it speaks.
- That the moment the package moves is the moment worth refreshing at. It is
  where the copy actually goes stale, and every other moment is somebody
  remembering.

## Wrong if

- A project wires the hook and `composer update` starts failing for something
  this package owns — a `.codex/config.toml` section it refuses to rewrite, a
  skills directory it cannot remove. Then dependency work is blocked by a dev
  tool, and the documented line has to be one that cannot fail the run, or it
  comes out of the documentation.
- Somebody wires it and the skills go stale anyway, because this package moved
  without a `composer update`: a path repository, or the standalone checkout
  every knowledge session uses. Then the hook is not the mechanism for the case
  it was taken for.
- This package starts writing prose into a file the user writes in as well. Then
  the fence is exactly what is missing, and this entry is why it was not already
  there.
