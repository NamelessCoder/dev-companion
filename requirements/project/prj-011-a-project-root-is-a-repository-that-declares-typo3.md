---
id: R-PRJ-011
title: 'A project root is a repository that declares TYPO3'
status: held
restsOn: [D-ANS-085]
heldBy:
  - InstanceTest::aNamedInstallationThatIsNotThereIsNotWalkedPast
  - InstanceTest::aPackageInsideAnInstalledProjectIsNotTheProjectRoot
  - InstanceTest::aProjectRootIsRecognisedByWhatItsOwnManifestDeclares
  - ProjectTest::aRepositoryThatSaysNothingAboutTypo3IsNotDescribedAsOne
---

# R-PRJ-011 — A project root is a repository that declares TYPO3

**The repository the project answer describes is one whose own `composer.json`
declares TYPO3, and the walk claims no other.**

Three declarations say it: the root is a TYPO3 package itself, it requires one
of TYPO3's own `typo3/cms-*` packages, or it carries the `extra.typo3/cms` block
TYPO3's Composer installer reads. Each is a decision somebody wrote down, which
a directory holding a `composer.json` is not — and the search walks up twelve
directories from wherever the client started the server, so a rule admitting any
manifest answers for whatever PHP repository the caller happens to be standing
below.

The installation is looked for first and over the whole walk, so a package
inside a project never displaces the project it sits in. Recognising a root is
also not finding an installation: nothing is installed below it, and every other
tool goes on saying so rather than speaking for a checkout with no packages and
no console.

## From

`feedback/2026-08-18-070333` (2026-08-18), a fresh clone of
`github.com/TYPO3GmbH/blog` with no `vendor/`, no `.build/` and no `config/`.
`typo3_project_describe` answered `cause: no-installation` and nothing else,
because the root that was found had to hold Composer's installed metadata — and
the session read `composer.json`, `package.json` and `.ddev/config.yaml` out of
the checkout by hand instead. Widening what counts as a root is what answers it,
and `D-ANS-085` names admitting too much as the way that goes wrong.
