# EXT-05 — Tests for the extension

**Environment:** `E-EXT` · **Contract:** `held` — `R-KNW-15`,
`R-SKL-2`

> Set up tests for this extension — unit and functional — and wire them into our
> GitHub Actions so every pull request runs them against all supported TYPO3
> versions.

**What the agent needs from this server**

- Testing conventions that transfer: what belongs in a unit test, what needs a
  functional one, how fixtures are kept deterministic, and where tests sit.
- A workflow that inspects and proves the existing harness before changing it,
  then establishes or repairs only the layers the extension actually needs.
- The extension-test configuration, bootstrap and database requirements that
  exist outside the core repository, plus versioned documentation where the
  installed framework or CI matrix has to decide.

**What has to come out of it**

- Existing tests, configuration, dependency constraints and commands are
  preserved and extended instead of replaced by a parallel harness.
- Unit and functional commands run locally before CI calls those same commands;
  dependency versions and matrix combinations are resolved from the package
  rather than copied from another extension.
- Every established layer is proved by a meaningful test or an honestly empty
  discovered suite. No core `runTests.sh` command and no vacuous green test is
  offered.
- Functional database prerequisites are separate from assertion failures, and
  credentials are not committed.

**How it fails**

- A core suite or copied configuration that cannot run in the extension.
- CI YAML written before its local command has passed.
- Existing working tests or scripts replaced because the task was treated as a
  blank project.
- `assertTrue(true)` presented as proof that the harness exercises the extension.
