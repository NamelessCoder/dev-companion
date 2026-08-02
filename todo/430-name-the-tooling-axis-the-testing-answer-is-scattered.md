# Name the tooling axis: the testing answer is scattered over four shapes

**Serves:** R-SCO-6, decisions/

One row of the knowledge base — how a change is tested — is stored four ways:
the core harness in `knowledge/test-suite-hints.json`, a file of its own that is
`runTests.sh` and nothing else; the core conventions as the `core-tests` hint in
`architecture-hints/php.json`; the project harness and conventions together as
`project-extension-tests` in the same file; the browser layer as `browser-tests`
in `general.json`; and the scripts themselves as
`knowledge/documents/typo3-core-scripts.md`. Three tools reach into that, and
`extension-static-analysis` is the same shape again for PHPStan.

The reading behind this is `D-AUD-4`: `typo3_test_run_guide` was never a
core-only tool, it was the core column of the testing row, and the profile
deleted the tool because there was no way to say "this row answers differently
here". Where a harness ships as a Composer package, the tooling row falls out of
the package axis for free and no separate one is needed; `runTests.sh` is a
script in one repository and falls out of nothing.

The step is to settle whether tooling is stored as a dimension crossing the
audience one, or stays what it is now — reachable by keyword through
`appliesTo` and addressable as nothing. If it becomes one, every cell has to
carry the versions it was true for: PHPStan levels move, Rector sets are
renamed, and `src/Upkeep/TestingFramework.php` already exists because
`typo3/testing-framework` tracks majors. Tooling advice rots faster than the
conventions around it, which is the argument for the axis and the reason it is
the expensive one. Depends on `todo/420`, which decides how an entry says who it
is for at all.
