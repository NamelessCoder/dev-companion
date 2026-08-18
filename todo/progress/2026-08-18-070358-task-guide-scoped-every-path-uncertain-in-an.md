# Place the repository from its root manifest, not from its installed packages

**Serves:** feedback/2026-08-18-070358-task-guide-scoped-every-path-uncertain-in-an.md
**Priority:** high
**Branch:** todo/task-guide-scoped-every-path-uncertain-in-an
**Claimed:** 2026-08-18

Judged on 2026-08-18 and queued: the scoping is the defect, and the suppression
`R-SCO-002` performs had nothing to fire on. `D-SCO-012` carries the reading and
the two fixtures it was reproduced against, including why failing closed on
`uncertain` is declined.

Three rungs of `Scope::of()` are silent until `composer install` has run, so an
extension repository is `uncertain` in the state it is cloned in:

1. `Instance::startedIn()` names a repository only as a core checkout or a
   populated Composer project. Read the root `composer.json` instead — a root of
   type `typo3-cms-extension` is not a core checkout, with no path argument at
   all — without making a repository report an installation, which is
   `D-DIS-001`'s line.
2. An extension key passed as a path resolves through
   `Instance::isSystemExtension()`, which needs `packages()`.
   `Instance::rootPackage()` already reads the key from the root manifest and
   `composerPackages()` withholds it while the vendor metadata is empty.
3. `ltrim($path, './')` strips the leading dot, so nothing can ever match the
   `.ddev/` entry in `Scope::PROJECT_WORK`.

`R-SCO-001` keeps its order — only what the rungs are allowed to read moves —
and the fix adds the cases that hold it on a repository with no installation
under it to its **Held by**.
