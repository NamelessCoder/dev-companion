# Carry what four revoked entries still hold

**Serves:** decisions/
**Priority:** normal
**Run:** bin/cli decisions:list | grep revoked

`D-DOC-055` settled that where a revoked entry has a part that still holds, a
decision covering that part is what carries it and `revokedBy` names it. Two
entries were that pattern with the link missing and now name their successor:
`D-DIS-002` → `D-DIS-007`, `D-KNW-001` → `D-KNW-006`.

Four are left where the entry's own `Revoked on` names a part that still holds
and no decision carries it. Each needs one reading against what the repository
does today, and then either a new decision or a bullet saying why none is owed:

- **`D-CAT-002`** — the revocation says the entry it named now carries four
  files that `catalog:check` reads, and that "for the other six, existence is
  still all that is checked". Nothing states which of the two a reference entry
  gets, or what decides it.
- **`D-KNW-002`** — the pairing was off by one release line and the revocation
  names the corrected one, 8.3.3 / 9.6.1 / `main`. It also says the statements
  are untouched. `src/Upkeep/TestingFramework.php` is where the pairing lives
  now, and no entry says so.
- **`D-SCO-001`** — "The `typo3_test_run_guide` still declines because its
  answer shape is a core suite invocation." That half is stated nowhere else;
  what was revoked is the assumption that nothing described an extension's
  harness.
- **`D-SCO-004`** — "The withholding held and the sentence about the escape did
  not." The withholding is the half that holds, and the escape wording is what
  went wrong.

`D-DIS-003` is the fifth revocation without a successor and is not on this list:
its `Revoked on` replaces the mechanism whole rather than naming a part that
survived. Reading it to confirm that is part of the step.

What this is not is a sweep over the 28 revocations that do name a successor.
`revokedBy` naming one is not proof it carries every part that still holds, but
nothing here says one of them does not, and a reading of 28 entries is a
different step from this one.
