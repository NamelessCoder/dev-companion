---
id: D-SCO-012
title: The root manifest places the work before the dependencies are installed
date: 2026-08-18
status: open
coveredBy:
  - InstanceTest::aRepositoryWithNoInstallationAroundItIsNotReportedAsOne
  - ScopeTest::aBriefInAnExtensionRepositoryHandsBackNoCoreSuite
  - ScopeTest::aDotfileKeepsItsDotWhenAPathIsNormalised
  - ScopeTest::anExtensionRepositoryIsPlacedByItsRootManifest
  - ScopeTest::theDeclaredExtensionKeyPlacesAPath
---

# D-SCO-012 — The root manifest places the work before the dependencies are installed

**The repository the session stands in is placed from its root `composer.json`
rather than from its installed packages, so scope is decided before
`composer install` has run.**

`feedback/2026-08-18-070358` reported `typo3_task_guide` scoping every path of
an extension repository `uncertain` and handing back `runTests.sh` suites into a
repository that has no `Build/Scripts/`. The scoping is the whole of it: with
the scope wrong, the suppression `R-SCO-002` already performs had nothing to
fire on.

## Evidence

- Two fixtures of the same repository, a root `composer.json` of type
  `typo3-cms-extension` declaring `extra.typo3/cms.extension-key: blog` and a
  `.ddev/config.yaml`, differing only in whether `vendor/` holds Composer's
  metadata. Fresh, `Scope::of()` answers `uncertain` for `.ddev/config.yaml`,
  `composer.json` and `blog` alike, which is what the feedback recorded.
  Installed, the same three answer `project`, `project` and `extension`.
- Three rungs are silent on the fresh clone, and every one of them for the same
  reason. `Instance::isSystemExtension()` reads `packages()`, so an extension
  key passed as a path — the affordance the `paths` parameter documents —
  resolves only once Composer's metadata exists. `Instance::startedIn()` names
  two kinds and both need an installation: `typo3-cms-core` at the root, or a
  populated vendor directory. An extension repository before its install is
  neither.
- The key is on disk and already parsed. `Instance::rootPackage()` reads
  `extra.typo3/cms.extension-key` from the root manifest, and
  `composerPackages()` withholds what it returns while `$packages === []`.
- `.ddev/` is dead as a marker. `Scope::of()` normalises with
  `ltrim($path, './')`, which strips the leading dot of a dotfile, so no path
  can match the `.ddev/` entry in `PROJECT_WORK`. It reads `project` in the
  installed fixture from the last rung instead, which is why nothing has
  noticed: the entry is consulted only where no installation places the session,
  and that is the one state it is needed in.

## Decided

- Queued rather than closed. The change is in `src/`, and what it moves is the
  order `R-SCO-001` states, so it is reviewed rather than made in the judging
  run.
- The last rung reads the manifest at the root, which is a decision somebody
  wrote rather than a directory that has to be populated: a root declaring
  `typo3-cms-extension` is not a core checkout, and that is answerable with no
  path argument at all.
- The manifest is read whether or not anything is installed under it, so the
  repository is placed the same way in both states. The vendor directory decides
  what can be read from an installation and says nothing about which repository
  this is, and a rung that turns as `composer install` runs is the blindness
  this entry is about rather than a second reading of it. What moves with it: an
  installed extension repository is `extension` where it was `project`, which is
  what it was all along.
- Only `typo3-cms-extension` at the root. `typo3-cms-framework` is what every
  system extension of the core declares, so reading it would place a contributor
  standing in `typo3/sysext/backend/` outside the core — the back half of
  `D-SCO-005`'s first **Wrong if**, arrived at from the other side.
- Failing closed on `uncertain` is not the lever, and the feedback's second
  suggestion is declined. `D-SCO-008` settled that `uncertain` still carries the
  core's own answer, because there is no second body of conventions to hand
  over, and its second **Wrong if** describes this feedback exactly: `uncertain`
  became the common answer, so the last rung is wrong rather than the value.
  Withholding the checks would have left the same repository unplaced and
  answered it with less.
- `D-DIS-001` stays as written. Its second **Assumed** — a root package alone is
  not an installation — is about what may be read from an installation, and
  placing the work must not start reporting one where there is a repository.

## Assumed

- The fresh clone is the ordinary state for this question rather than the exotic
  one. An extension repository is cloned before it is installed, and
  `feedback/2026-08-18-070333` reports the same blindness from the other side:
  `typo3_project_describe` answering nothing on the clone, from files that were
  all present.

## Wrong if

- Reading the root manifest for scope makes an extension repository report an
  installation, and the icon, label and package answers start speaking for a
  checkout with no console behind it. That is `D-DIS-001`'s line, and the two
  questions have to stay apart the way `D-SCO-005` already keeps
  `TYPO3_DEV_COMPANION_ROOT` out of this one.
- A monorepo whose root declares a TYPO3 package type while the work is in a
  package below it. The root then places every path that carries nothing of its
  own, and `D-DIS-001`'s **Wrong if** already names the shape as unseen here.
- A path in an installed extension repository needs the project's answer rather
  than the extension's. The root now places every path that carries nothing of
  its own, and `.ddev/` is the only project marker such a repository has.
- `uncertain` stays common once the rung is fixed. Then what places the work is
  not in the call at all and has to be asked for at initialize time, which is
  where `D-SCO-008` pointed and this entry does not go.
