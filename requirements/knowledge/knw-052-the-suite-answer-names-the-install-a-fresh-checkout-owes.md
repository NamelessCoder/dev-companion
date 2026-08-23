---
id: R-KNW-052
title: 'The suite answer names the install a fresh checkout owes'
status: held
restsOn: [D-KNW-041]
heldBy:
  - KnowledgeTest::theInvocationNotesNameTheInstallAFreshCheckoutOwes
---

# R-KNW-052 — The suite answer names the install a fresh checkout owes

**The invocation notes of the test-suite corpus state that a suite runs against
the `vendor/` of the checkout it is started from, and name the command that puts
one there.**

`runTests.sh` mounts the checkout and nothing else, so the dependencies a suite
finds are the ones in that directory. A git worktree of a core checkout has
none: `/vendor/*` and `/bin/*` are gitignored, and git therefore never brings
them. The first suite fails on `bin/phpunit: not found`, which names phpunit
rather than the checkout, so the cause is not readable from the symptom.

The obvious repair fails in the same way. A `vendor/` symlinked from the
original checkout points outside the one bind mount, and nothing inside the
container can follow it.

The note belongs with the invocation rather than with one suite, because it
holds for every suite the script offers, and the caller reads it before choosing
one.

This is the neighbour of
[`R-KNW-049`](knw-049-a-check-that-can-pass-without-reading-anything-says-so.md):
that one keeps a check that read nothing from being taken for one that passed,
and this one keeps a checkout that can run nothing from being handed a command.

## From

A core patch session in a fresh git worktree, spending four round trips and one
long install before a suite ran — the failing run, the symlink, the second
failing run, `composerInstall`, then the real run (2026-08-02). Reproduced in a
worktree of `.checkouts/typo3.git` on 2026-08-03, which is the run
[`D-KNW-041`](../../decisions/knowledge/knw-041-the-checkout-a-suite-is-started-in-supplies-its-own-dependencies.md)
records.
