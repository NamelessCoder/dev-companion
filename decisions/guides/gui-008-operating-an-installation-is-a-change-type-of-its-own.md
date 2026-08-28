---
id: D-GUI-008
title: Operating an installation is a change type of its own
date: 2026-08-03
status: open
coveredBy:
  - HintsTest::workThatOperatesAnInstallationIsAnsweredWithABootBrief
---

# D-GUI-008 — Operating an installation is a change type of its own

**`typo3_task_guide` has a `changeType` value for work that operates an
installation rather than changing its code. The intent it reaches is the one
that boots an environment, not the one that creates an installation.**

A session booting a Composer project from a fresh clone was told to confirm the
target branch, keep the patch focused, add test coverage and write a commit
message — for work that writes no file.

## Evidence

- `feedback/2026-08-03-154508`, re-run on 2026-08-03 in this repository with the
  query it states:
  `task="Boot up a TYPO3 project locally for the first time from a fresh clone: install dependencies, start the local environment, import the demo database and fileadmin, build frontend assets, create a backend user, verify the site responds"`,
  `changeType="unknown"`, no area and no paths. What comes back is what the
  report says, item for item — "Confirm the target TYPO3 core branch and issue
  context", "Keep the patch focused on the stated task", "Add or update the
  narrowest useful test coverage", and the commit-message step, closing a list
  of five conditional items that are the `typo3 setup` command's.
- One needle fired, and it is a weak one: `install`, of `installation-setup`. It
  matched the phrase `install dependencies`, which is Composer's install and not
  TYPO3's. `Text::containsWord()` matches inside a word, so the same needle also
  fires on every task text that contains `installation` — `run the installation`
  reaches it with no form of `install` standing alone in it.
- The five items that came back are properties of the setup command, read from
  `knowledge/task-intents.json`: the password it does not print, the install
  tool password it derives from the same value, the reset path for both, and
  `--create-site` beside a sitepackage. Each holds for creating an installation
  and none of them for starting one that exists.
- The corpus already carries the condition the missing value would settle. Both
  `installation-setup` and `installation-upgrade` end their `condition` with
  "rather than working on the code in one" and "rather than about the code in
  it" — two intents asking the caller for a fact the enum has no way to state.
- `bin/cli feedback:list` on 2026-08-03: 29 open, and five of them are tasks
  that operate an installation rather than change one. Two from
  `/home/benji/projects/site-demo-typo3-org` — this one and `2026-08-03-154501`
  — and three from `/home/benji/projects/ext-guidedtour`, `2026-08-03-162826`,
  `-162836` and `-162858`, which install, seed and run a local instance and name
  `typo3_task_guide` as a tool they called.
- `D-GUI-006` is the same failure in its first shape, and its own words are what
  this one runs into: `audit` is "the one type whose rules are stated
  elsewhere", the only value on the enum that changes nothing, and it asks for
  the brief a review needs. A boot is neither a review nor a patch, so `unknown`
  is the honest answer and `unknown` is the patch skeleton.

## Decided

- **A second value on the enum, and the skeleton forks on it.** `D-GUI-006`
  established that the checklist skeleton is what changes rather than what is
  appended to it, and the review skeleton it wrote is a review's — "report what
  the review did not reach" is not a step a boot takes either. So the two
  non-changing values do not share one skeleton.
- **The word is `operations`.** It has to be safe in the matcher, because
  `TaskGuide::answer()` feeds the change type to `TaskIntents::detect()`.
  Measured against every needle in the corpus, `operations` matches none, while
  `setup` and `install` are both `installation-setup`'s and would classify every
  boot as a creation — which is the failure this entry is about, in the shape
  the feedback's own suggestion would have taken.
- **The booting branch is an intent of its own**, beside `installation-setup`
  and `installation-upgrade` rather than inside either. This repository already
  separates creating from upgrading, and what it lacks is the third: bringing up
  an installation that exists, from what the repository declares — its
  environment, its data import, its asset build.
- **`installation-setup`'s weak `install` is narrowed by the same commit.** A
  needle that fires on the word `installation` reaches every task about an
  installation, and the value above is what a caller states instead.
- **The hint half of the feedback is taken here too.** Three of the four hints
  it got — `datahandler-basics`, `fal-basics`, `public-assets` — are PHP hints,
  and `php` is the only domain the call detected. `CHANGE_TYPE_TERMS` is the
  extra domain signal a change type carries into `Domains::detect()`, so the new
  value's terms are what moves that selection, and they are part of the same
  change rather than a second card.
- **Queued rather than closed on the spot.** It adds a value to a declared
  `inputSchema` enum and forks a skeleton in `src/`, which
  [judging.md](../../documentation/records/judging.rst) puts on the far side of
  the autonomous line.
- **`normal`, and the corpus is what set it.** One session reported the shape,
  and five reported the task: work that runs an installation reached this server
  five times on one day from two checkouts. What is not `high` about it is that
  no second session has said the brief was the wrong shape.
- **Not taken: the fallback itself.** `unknown` reaching the patch skeleton is
  what `D-GUI-006` decided and this entry does not reopen. What changes is that
  a caller who operates an installation has something else to state.

## Assumed

- The five `installation-setup` items are the creating branch's because each one
  is a property of the `typo3 setup` command. That was read off the corpus file
  and the feedback's account of running it, not from a run in this repository.
- Booting an installation that exists is a third kind of work rather than the
  first half of an upgrade. `installation-upgrade`'s checklist is a migration —
  dump the database, raise PHP, run the wizards — and none of it is what a fresh
  clone needs.
- A caller who classifies work as `operations` is not also changing code in the
  same call. `D-GUI-006` assumed the mirror of this for `audit`, and
  `todo/waiting/2026-08-01-115711` is that assumption under question.

## Wrong if

- A brief comes back without the patch steps for work that does change something
  — "fix the deploy hook so the import runs" is the form it would take. The
  answer is then the one `D-GUI-006`'s **Wrong if** names: the needle or the
  value carries a condition, rather than the shape being given up.
- The narrowed `install` needle stops reaching a task that really does create an
  installation. `feedback/2026-08-03-162826` — installing TYPO3 unattended from
  a shell script — is the task to measure it against.
- The booting intent's checklist turns out to be Composer's and DDEV's own
  documentation restated, with nothing about TYPO3 in it. Then what was missing
  was the environment's lifecycle in `typo3_project_describe`, which
  `feedback/2026-08-03-154501` reports, and not a shape in this brief.

## Since then

The value, the intent and the fork landed together, with `operations` a needle
rather than the id: the caller states what the work is, and the corpus keeps the
three installation intents named after the installation.

The terms entry could not be taken as decided and is empty. `CHANGE_TYPE_TERMS`
reaches the domains and with them the checks and the next lookups, while the
hints are matched from the paths and the task text and never see the change
type. So the hint half of that feedback is open, and what it needs is a
statement about the subject rather than a domain signal. The first **Wrong if**
holds and is held by less since `D-GUI-009` stopped filtering the intent out of
a call that states a type.
