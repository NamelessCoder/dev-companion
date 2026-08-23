---
id: D-DIS-019
title: A project root is found from what its manifest declares
date: 2026-08-18
status: open
coveredBy:
  - InstanceTest::aNamedInstallationThatIsNotThereIsNotWalkedPast
  - InstanceTest::aPackageInsideAnInstalledProjectIsNotTheProjectRoot
  - InstanceTest::aProjectRootIsRecognisedByWhatItsOwnManifestDeclares
  - InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne
  - ProjectTest::aRepositoryThatSaysNothingAboutTypo3IsNotDescribedAsOne
  - ProjectTest::theRepositoryIsDescribedBeforeAnythingIsInstalledInIt
---

# D-DIS-019 — A project root is found from what its manifest declares

**The project answer finds its root by what the repository's own `composer.json`
declares about TYPO3, and finding one is not finding an installation.**

`D-ANS-085` decided that the file-only half of `typo3_project_describe` is owed
wherever a project root is found, and named the root rule as where that goes
wrong: the search walks up twelve directories, so admitting any manifest reports
a TYPO3 project for every PHP repository above the caller.

## Evidence

- What a clone declares, from the repository `feedback/2026-08-18-070333` was
  recorded in: `"type": "typo3-cms-extension"`, a required
  `typo3/cms-core: "^13.4.15 || ^14.3"`, and
  `extra.typo3/cms.extension-key: blog`. Three declarations for one repository,
  and `D-SCO-012` reproduced the same shape as a fixture.
- What a site declares, read in `.environments/e-site-13.4/composer.json` on
  2026-08-18 — the `typo3/cms-base-distribution` every environment here is built
  from. `"type": "project"`, twenty-six required `typo3/cms-*` packages, and no
  `extra` block at all. The package types alone would walk past a site
  installation.
- What the rule has to refuse, in this repository's own manifest:
  `"type": "library"`, `mcp/sdk` and two Symfony components, and nothing of
  TYPO3's. A session working here reaches the same discovery.
- `typo3/coding-standards` and `typo3/tailor` are the shape a rule matching the
  vendor name would claim. Both are tools a repository uses on itself, and
  neither installs TYPO3.
- What `Instance::locate()` walked past: a Composer project was recognised by
  `composerPackages()` finding entries in `vendor/composer/installed.json`, the
  one file a clone does not have. A core checkout was recognised by its own
  `type` and so answered on a clone all along.

## Decided

- **Three declarations, all in the root's own manifest**: it is a TYPO3 package
  itself, it requires a `typo3/cms-*` package, or it carries the
  `extra.typo3/cms` block TYPO3's Composer installer reads. Each is something
  somebody wrote down, which is what a directory holding a `composer.json` is
  not.
- `require-dev` counts as much as `require`. A package that installs the core
  for its test setup alone is a TYPO3 repository, and it is the shape an
  extension that supports two majors is written in.
- The installation is looked for first and over the whole walk, so a package
  inside an installed project never displaces the project it sits in. Where
  nothing is installed anywhere up the walk, the nearest declaring root answers
  — the same rule the walk already followed.
- Recognising a root is not finding an installation. `Instance::describe()`
  keeps its meaning, so the icon, label and schema answers go on saying
  `cause: no-installation` rather than speaking for a checkout with no packages
  and no console, which is `D-DIS-001`'s line.
- A root named by `TYPO3_DEV_COMPANION_ROOT` and unusable is not walked past
  here either. The caller said which repository it means, and describing another
  one is the failure the variable is reported through.
- Nothing is remembered. The walk is one manifest read per directory, and the
  answer has to change the moment the install this state exists to prompt has
  run — which is `R-DIS-009` for the same reason.
- The state is said in `installed` rather than in `kind`. `kind` stays what the
  root declares itself to be, and a third value there would have carried the
  state at the price of the layout — a caller could no longer tell a site
  project from an extension repository, which is what the boot workflow decides
  on.

## Assumed

- That a session stands in the repository it means, or below it. The walk
  prefers the nearest declaring root, so a clone below another project's tree
  answers for itself.
- That no ordinary repository requires a `typo3/cms-*` package without being a
  TYPO3 repository. The prefix is TYPO3's own namespace for the CMS, and
  everything else it publishes is named outside it.
- That `extra.typo3/cms` is written only where TYPO3 is installed or shipped. It
  is read by `typo3/cms-composer-installers` and by nothing else this repository
  knows of.

## Wrong if

- A session reports the answer describing a repository that has nothing to do
  with TYPO3. Then one of the three declarations admits too much, and the
  installed-metadata gate was doing that work as well as the one it was blamed
  for.
- A session standing in `packages/<key>` of a clone is answered for that package
  while the environment, the document root and the sites it needs are one
  directory up. Then nearest-wins is wrong for an uninstalled tree and the
  outermost declaring root is the answer.
- A repository declaring TYPO3 only in `require-dev` is described as a project
  and the answer is read as being about a site. Then the two kinds of
  declaration are not one question.
- A tool other than `typo3_project_describe` starts answering from a root with
  nothing installed below it. Then the two questions have run together and
  `D-DIS-001`'s second **Assumed** no longer holds.
