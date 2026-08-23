---
id: D-DIS-010
title: What this package writes into a project ignores itself
date: 2026-08-03
status: open
coveredBy:
  - InstallerRecordTest::neitherCommandWritesIntoTheProjectsGitignore
  - InstallerTest::gitReportsTheProjectsOwnFilesAndNothingThisPackageWrote
---

# D-DIS-010 — What this package writes into a project ignores itself

**Each directory the install writes carries a `.gitignore` of its own saying
`*`, the record moves below one of them, and the project's `.gitignore` is left
to the project.**

Keeping generated skills out of a project's history was done from the project's
own `.gitignore`, so using this package meant changing a tracked file that
belongs to somebody else, on every run.

## Evidence

- What the block cost, measured on 2026-08-03 against the version before this
  change, in a project whose `.gitignore` held one line of its own: 14 lines
  after `install --agent=claude`, and 32 after a second and third client that
  read their skills from different paths. Nine skills times each distinct skills
  directory, plus the record, plus two markers.
- It was rewritten on every `install` and every `update`, by
  `ignoreGenerated()`, which read the file, replaced its own block whole and
  wrote it back. A project that had committed its `.gitignore` — which is the
  ordinary case — therefore had a diff in a tracked file each time somebody
  refreshed the skills.
- The same reading, done in a real repository on 2026-08-03: with a `.gitignore`
  saying `*` inside the published directory, `git status -uall` reports
  `?? .claude/skills/my-own-skill.md` and nothing else — the file ignores itself
  along with everything beside it, and the skill the project wrote is untouched
  by it. `git check-ignore -v` names that file as the rule for both.
- `documentation/usage/installing.rst` already drew the line this entry extends:
  merged agent or MCP configuration "is not ignored, because the project may
  share it". A `.gitignore` is shared more than any of those files, and it was
  the one this package wrote into.
- The record was the one artefact with no other option.
  `typo3-dev-companion.json` sat at the project root, and a file cannot ignore
  itself.
- The package is not published on Packagist and is required from a local
  checkout, which `documentation/usage/installing.rst` says outright. Every
  project that has the old layout was set up by hand from a checkout somebody
  owns.

## Decided

- Each published skill directory and `.typo3-dev-companion/` get `.gitignore`
  with `*`. The effect is written where the effect belongs, and it scales with
  nothing: one file per directory this package already replaces whole.
- The record moves to `.typo3-dev-companion/state.json` and is read from nowhere
  else.
- Nothing migrates a project that a development build set up. The old record and
  the old block are left where they are, and the code that would find them is
  gone rather than kept for a case that is somebody's own checkout: this package
  is unreleased, and carrying a reader for a layout no released version ever had
  is a second shape to keep working for as long as anybody remembers why.
- Against `.git/info/exclude`. It is not a file the project shares, which is the
  whole objection answered — but it is still a file this package does not own,
  it holds for one clone rather than for the project, and finding it means
  resolving a git directory through worktrees and submodules.
- Against ignoring nothing and leaving the choice to the project. That is the
  honest reading of "generated content is the project's business", and it costs
  every install nine untracked directories per skills path in `git status`,
  which is the noise this was keeping out in the first place.

## Assumed

- That a project wants the published skills out of its history. That was assumed
  before this change as well, and whoever disagrees is not blocked: `git add -f`
  reaches a file below an ignored directory the same as any other.
- That no client refuses, or reads, a `.gitignore` sitting in a skill directory.
  What each client looks for there is `SKILL.md` and the references beside it;
  nothing establishes what any of them does with an unexpected dotfile.
- That nobody is running a development build of this package in a project they
  do not own. It is required from a local path repository, so this holds by the
  way the package is installed rather than by anything asked of anybody.

## Wrong if

- A client stops finding a published skill after this, or reports the extra file
  as part of the skill. Then the ignore belongs one level up, at a directory
  this package owns outright, and the skills are published below it.
- Somebody's checkout loses skills they expected to have committed — a team that
  deliberately shares the published directories, where the `*` now hides them
  from the commit that would carry them. Then this is a choice the project makes
  rather than a property of the package.
- The block a development build left behind outlives the reason for it in a
  project somebody works in: `install` writes the new layout beside it, the old
  lines ignore directories that ignore themselves, and nothing says so. Then not
  migrating cost somebody a confused reading of their own `.gitignore`, and the
  release notes are what has to carry it.
