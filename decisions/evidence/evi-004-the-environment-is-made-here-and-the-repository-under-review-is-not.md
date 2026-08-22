---
id: D-EVI-004
title: The environment is made here, and the repository under review is not
date: 2026-08-02
status: open
coveredBy:
  - EnvironmentsTest::everyEnvironmentAScenarioNamesSaysWhereItComesFrom
  - EnvironmentsTest::everyEnvironmentThatIsNotMadeHereSaysWhereItComesFromInstead
  - EnvironmentsTest::theInstallationIsBuiltAtTheCoveredStableVersion
  - EnvironmentsTest::theBuildRequiresTheExtensionsWhoseConsoleCommandsThisServerAsksFor
  - EnvironmentsTest::theSetupStepPassesEveryOptionItCannotBeAskedFor
  - EnvironmentsTest::theSiteIsCreatedForTheAddressDdevGivesTheProject
  - EnvironmentsTest::everyStepOfTheBuildRunsInTheProjectRatherThanOnTheMachine
  - EnvironmentsTest::whatIsMadeHereIsNeverCommitted
---

# D-EVI-004 — The environment is made here, and the repository under review is not

**This repository makes the working directory a run needs in order to reach an
installation, and goes on declaring the third-party checkouts a review is
about.**

The line is what a case needs from the directory. Where that is a property this
repository can state — a Composer installation under DDEV, at the covered stable
version, whose console answers — it is built by `bin/cli environment:create`.
Where it is a property of somebody else's repository at a real revision, it
stays named in `todo/reference/`.

## Evidence

- All five environments in `scenarios/readme.md` sat on one machine. A forward
  run was therefore reproducible for whoever owned it, and for nobody else.
- The console half of this server is exercised by no test.
  [`D-DIS-007`](../discovery/dis-007-the-ddev-console-is-named-by-the-mount-not-by-the-variable.md)
  and
  [`R-DIS-018`](../../requirements/discovery/dis-018-a-console-command-never-inherits-the-clients-stdin.md)
  were both found by a real run in somebody's project, and the second cost two
  `REVIEW-02` attempts 24 minutes apart before anybody could name it.
- A build was measured rather than estimated, on 2026-08-02. Six commands, all
  `ddev`, from an empty directory to a frontend answering 200 and `site:list`,
  `language:domain:search`, `debug:backend:modules` and `fluid:namespaces` all
  answering through `Typo3Cli`. It ran in 27 seconds on a warm Composer cache
  and a few minutes on a cold one.
- The base distribution does not require `typo3/cms-lowlevel`, which carries
  `language:domain:search` and `configuration:show`. `scenarios/readme.md`
  defines `E-SITE` by that first command, so a plain base distribution is not
  one.
- `typo3/cms-install` 14.3.5 reads `--server-type`'s default through the same
  fallback as `TYPO3_SETUP_ADMIN_*` reads theirs. With `--no-interaction` and
  the option unset, `SetupCommand::getServerType()` hands its validator `false`
  and the setup dies on the type. Nothing in the option's definition says so.
- `bin/cli checkouts:update` is the precedent the cost was weighed against:
  gitignored, made by one command, re-fetchable at any time. An installation is
  not cheap in the same way, which is why it is made on demand rather than by
  `todo:claim` the way a checkout link is.

## Decided

- `E-SITE` and `E-NONE` are made here, below `.environments/`, gitignored the
  way `.checkouts/` is. One command makes each, and every step of it is a `ddev`
  command printed before it runs.
- The installation is TYPO3's own base distribution at the branch
  `knowledge/versions.json` marks stable, plus the system extensions this
  server's console path asks for and nothing else. A `composer.json` written
  here would be a second opinion on the shape of a site installation, and the
  one that goes stale.
- `E-EXT` stays declared. What a case needs from an extension repository is its
  real infrastructure at a real revision — complete in one checkout, incomplete
  in another, a major behind in a third — and a scaffold would supply this
  repository's own idea of all three.
- A made `E-SITE` is the environment and never the subject of a recorded forward
  review. Its defects would be the ones this repository wrote, which is
  [`D-EVI-001`](evi-001-forward-evidence-comes-from-a-review.md) from the other
  side. What it is for is the contract cases, and a real installation for the
  half of this server that has never had one.
- No site package is scaffolded, for the same reason. `scenarios/readme.md`
  defines `E-SITE` without one, and `REVIEW-01` reviews a project **and its site
  package**, so that review keeps naming a real project.
- The admin password is a constant in the source. The environment exists to be
  logged into by whoever runs a case in it, it guards a throwaway site on
  `*.ddev.site`, and a generated secret would put the environment back on the
  machine that made it.
- Rejected: cloning the three extension checkouts at pinned revisions, the way
  the core is cloned. It would work, and it would make this repository assert a
  revision of somebody else's repository — including the `--single-branch`
  constraint the `news` checkout carries, which one `git fetch` by a later
  session would quietly end.
- Rejected: sharing one environment across worktrees by symlink, as
  `.checkouts/` is shared. That one is read; this one has a database two
  sessions would write at once.

## Assumed

- That a DDEV project may sit below this checkout. DDEV refuses a project nested
  inside another one, and nothing above `.environments/` is a DDEV project — but
  that is a property of where somebody clones this repository, not of the
  repository.
- That `typo3/cms-base-distribution` keeps tracking the covered majors and stays
  the shape a site installation starts in.
- That the machine has a docker daemon. It is what the command refuses on, and a
  CI job without one can run everything else here.

## Wrong if

- A recorded forward run appears whose environment is the made one. The findings
  would then be this repository's own scaffold read back to it, which is the
  failure `D-EVI-001` names.
- The build stops reproducing what it reproduced here — a step that needs a
  person, a distribution that no longer covers the stable major, a DDEV flag
  that changes meaning. `EnvironmentsTest` holds the commands and not the
  containers, so this surfaces as a build that fails rather than as a test that
  does.
- `E-EXT` turns out to be the environment cases actually fail in, and the three
  declared checkouts drift apart from what the reference says they play. Then
  what was rejected here — pinning them — is worth its cost after all.

## Since then

The second **Wrong if** arrived on 2026-08-02, on the second checkout to run the
build, and by a route it did not anticipate. Nothing about DDEV or the
distribution had changed: the build stopped reproducing because the *first* run
had happened. A worktree that made an environment and was then removed leaves
the project name registered against an approot DDEV reports as
`project directory missing`, and the guard here refused in the name of a
checkout nobody could visit. Behind it sat the database, a volume named after
the project rather than the directory, which is what a second build under the
same name met at the setup step as
`The selected database contains already 42 tables.` — past `--force`, which
reaches the settings file alone.

Both are the same leftover and
[`D-EVI-005`](evi-005-a-registration-nothing-can-reach-is-cleared-with-its-database.md)
clears them together. What that says about this entry is narrower than the
**Wrong if** reads: an environment made on demand and gitignored is not thereby
free, because the part of it that is global to the machine outlives the checkout
that asked for it. The decision to make it here stands, and the measured build
was re-run whole under the fix — 32 seconds, frontend 200, the console
answering.

"The installation is TYPO3's own base distribution at the branch
`knowledge/versions.json` marks stable" was one installation, and it is one per
covered version that has a release since
[`D-EVI-006`](evi-006-one-installation-per-covered-version-kept-and-started.md):
a case naming another covered line was run on the stable one or not at all. That
entry also carries the number this one weighed against `.checkouts/` and never
measured — about 260 MB a line. The stable branch is still what a case that
names no version is made of, and everything else here stands.
