# TYPO3 Core Contribution Rules

This file is the first local knowledge base for the TYPO3 CMS MCP server. Keep
it practical, versioned, and conservative. Prefer links to official TYPO3
documentation when rules depend on a specific branch or current policy.

## Contribution Flow

- Work against the TYPO3 core repository and target the intended active branch.
- Keep changes focused on one bug, feature, cleanup, or test improvement.
- Add or update tests for behavior changes.
- Run the narrowest useful test first, then broaden when shared behavior is
  touched.
- Mention affected subsystems and executed commands in the final task summary.

## Code Style

- Follow the coding style already used by the touched TYPO3 subsystem.
- Prefer existing TYPO3 APIs and services over new local abstractions.
- Keep public API changes explicit and documented.
- Avoid unrelated refactoring in bug fix patches.

## Testing

- Unit tests are expected for isolated behavior.
- Functional tests are expected for persistence, configuration, routing, backend
  behavior, or integration with TYPO3 services.
- Acceptance or UI tests are useful when the change affects editor or
  administrator workflows.
- Document tests that could not be executed and why.

## XLIFF Label Lifecycle

- Do not delete a `trans-unit` from an XLF file. Removing a label breaks
  third-party code and translation overrides that still reference the key, and
  it drops the translator history on translate.typo3.org.
- Retire a label by marking its `trans-unit` with
  `x-unused-since="<next upcoming version>"`, for example
  `<trans-unit id="wizard.progress" x-unused-since="15.0">`. The version is the
  release the label becomes unused in, not the current one.
- Since TYPO3 v14.1 a label marked that way raises an `E_USER_DEPRECATED` error
  when it is resolved, so integrators find remaining usages. XLIFF 2.0 uses
  `subState="deprecated"` on the segment for the same purpose.
- Remove the marked `trans-unit` only in the breaking-change patch of a later
  major release, together with the other deprecation removals.
- A label introduced and dropped again within the same patch is simply removed —
  it was never released, so there is nothing to deprecate.
- Marking a label unused is a deprecation: it needs a changelog entry, and
  usages inside the core have to be migrated in the same patch.
- Run `./Build/Scripts/runTests.sh -s checkIntegrityXliff` and
  `./Build/Scripts/runTests.sh -s normalizeXliff` after editing XLF files.

## Review Readiness

- The change should be reproducible from the issue or task description.
- The patch should include a concise explanation of the problem and the chosen
  fix.
- Breaking changes, migrations, and deprecations need clear notes.
- Security-sensitive behavior needs extra care and focused tests.
